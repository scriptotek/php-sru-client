
[![Build Status](http://img.shields.io/travis/scriptotek/php-sru-client.svg?style=flat-square)](https://travis-ci.org/scriptotek/php-sru-client)
[![Coverage Status](http://img.shields.io/coveralls/scriptotek/php-sru-client.svg?style=flat-square)](https://coveralls.io/r/scriptotek/php-sru-client?branch=master)
[![Code Quality](http://img.shields.io/scrutinizer/g/scriptotek/php-sru-client/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/scriptotek/php-sru-client/?branch=master)
[![Latest Stable Version](http://img.shields.io/packagist/v/scriptotek/sru-client.svg?style=flat-square)](https://packagist.org/packages/scriptotek/sru-client)
[![Total Downloads](http://img.shields.io/packagist/dt/scriptotek/sru-client.svg?style=flat-square)](https://packagist.org/packages/scriptotek/sru-client)

## php-sru-client

Simple PHP package for making [Search/Retrieve via URL](http://www.loc.gov/standards/sru/) (SRU) requests, using the 
[Guzzle HTTP client](http://guzzlephp.org/)
and returning 
[QuiteSimpleXMLElement](//github.com/danmichaelo/quitesimplexmlelement) instances. Includes an iterator to easily iterate over search results, abstracting away the process of making multiple requests.

If you prefer a simple text response, you might have a look at
the [php-sru-search](https://github.com/Zeitschriftendatenbank/php-sru-search) package.

### Install using Composer

Add the package to the `require` list of your `composer.json` file.

```json
{
    "require": {
        "scriptotek/sru-client": "dev-master"
    },
}
```

and run `composer install` to get the latest version of the package.

### Laravel 5 integration

In the $providers array add the service providers for this package:

    Scriptotek\Sru\Providers\SruServiceProvider::class,

Add the facade of this package to the `$aliases` array:

    'SruClient' => Scriptotek\Sru\Facades\SruClient::class,

Publish configuration in Laravel 5:

    $ php artisan vendor:publish --provider="Scriptotek\Sru\Providers\SruServiceProvider"

The configuration file is copied to `config/sru.php`.

### Example

```php
require_once('vendor/autoload.php');
use Scriptotek\Sru\Client as SruClient;

$url = 'http://sru.bibsys.no/search/biblioholdings';

$client = new SruClient($url, array(
    'schema' => 'marcxml',
    'version' => '1.1',
    'user-agent' => 'MyTool/0.1'
));
```

To get the first record matching a query:
```php
$client->first('bs.isbn="0415919118"');
```
The result is a [Record](//scriptotek.github.io/php-sru-client/api_docs/Scriptotek/Sru/Record.html)
object, or `null` if not found.

To iterate over all the results from a `searchRetrieve` query, use the [Records](//scriptotek.github.io/php-sru-client/api_docs/Scriptotek/Sru/Records.html) object returned from `Client::records()`. The first argument is
the CQL query, and the second optional argument is the number of records to fetch for each request (defaults to 10).

```php
$records = $client->records('dc.title="Hello world"');
if ($records->error) {
	print 'ERROR: ' . $records->error . "\n";
}
foreach ($records as $record) {
	echo "Got record " . $record->position . " of " . $records->numberOfRecords() . "\n";
	// processRecord($record->data);
}
```

Use explain to get information about servers:

```php
$urls = array(
    'http://sru.bibsys.no/search/biblio',
    'http://lx2.loc.gov:210/LCDB',
    'http://services.d-nb.de/sru/zdb',
    'http://api.libris.kb.se/sru/libris',
);

foreach ($urls as $url) {

    $client = new SruClient($url, array(
        'version' => '1.1',
        'user-agent' => 'MyTool/0.1'
    ));

    $response = $client->explain();

    if ($response->error) {
        print 'ERROR: ' . $response->error . "\n";
        continue;
    }

    printf("Host: %s:%d\n", $response->host, $response->port);
    printf("  Database: %s\n", $response->database->identifier);
    printf("  %s\n", $response->database->title);
    printf("  %s\n", $response->database->description);
    print "  Indexes:\n";
    foreach ($response->indexes as $idx) {
        printf("   - %s: %s\n", $idx->title, implode(' / ', $idx->maps));
    }

}
```

### API documentation 

API documentation can be generated using e.g. [Sami](https://github.com/fabpot/sami),
which is included in the dev requirements of `composer.json`.

    php vendor/bin/sami.php update sami.config.php -v

You can view it at [scriptotek.github.io/php-sru-client](//scriptotek.github.io/php-sru-client/)
