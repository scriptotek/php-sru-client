
[![Build Status](http://img.shields.io/travis/scriptotek/php-sru-client/master.svg?style=flat-square)](https://travis-ci.org/scriptotek/php-sru-client)
[![Coverage](https://img.shields.io/codecov/c/github/scriptotek/php-sru-client/master.svg?style=flat-square)](https://codecov.io/gh/scriptotek/php-sru-client)
[![Code Quality](http://img.shields.io/scrutinizer/g/scriptotek/php-sru-client/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/scriptotek/php-sru-client/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/aa33625a-f677-4bd8-8bf3-9230153dced8/mini.png)](https://insight.sensiolabs.com/projects/aa33625a-f677-4bd8-8bf3-9230153dced8)
[![Latest Stable Version](http://img.shields.io/packagist/v/scriptotek/sru-client.svg?style=flat-square)](https://packagist.org/packages/scriptotek/sru-client)
[![Total Downloads](http://img.shields.io/packagist/dt/scriptotek/sru-client.svg?style=flat-square)](https://packagist.org/packages/scriptotek/sru-client)

# php-sru-client

Simple PHP package for making [Search/Retrieve via URL](http://www.loc.gov/standards/sru/) (SRU) requests, using the [Guzzle HTTP client](http://guzzlephp.org/) and returning
[QuiteSimpleXMLElement](//github.com/danmichaelo/quitesimplexmlelement) instances. Includes an iterator to easily iterate over search results, abstracting away the process of making multiple requests.

If you prefer a simple text response, you might have a look at
the [php-sru-search](https://github.com/Zeitschriftendatenbank/php-sru-search) package.

## Install using Composer

Make sure you have [Composer](https://getcomposer.org) installed, then run

```bash
composer require scriptotek/php-sru-client
```

in your project directory to get the latest stable version of the package.

**You also need a HTTP library**. If you're not already using one in your project,
just add Guzzle:

```bash
composer require php-http/guzzle6-adapter
```

[HTTPlug discovery](http://php-http.readthedocs.io/en/latest/discovery.html) is
used in order to not depend on a specific library.

## Configuring the client

```php
require_once('vendor/autoload.php');
use Scriptotek\Sru\Client as SruClient;

$sru = new SruClient('http://bibsys-network.alma.exlibrisgroup.com/view/sru/47BIBSYS_NETWORK', [
    'schema' => 'marcxml',
    'version' => '1.2',
    'user-agent' => 'MyTool/0.1',
]);
```

## Search and retrieve

To get all the records matching a given CQL query:

```php
$records = $sru->all('alma.title="Hello world"');
foreach ($records as $record) {
	echo "Got record " . $record->position . " of " . $records->numberOfRecords() . "\n";
	// processRecord($record->data);
}
```

where `$record` is an instance of [Record](//scriptotek.github.io/php-sru-client/api_docs/Scriptotek/Sru/Record.html) and `$record->data` is an instance of [QuiteSimpleXMLElement](https://github.com/danmichaelo/quitesimplexmlelement).

The `all()` method takes care of continuation for you under the hood for you;
the [Records](//scriptotek.github.io/php-sru-client/api_docs/Scriptotek/Sru/Records.html) generator
continues to fetch records until the result set is depleted. A default batch size of 10 is used,
but you can give any number supported by the server as a second argument to the `all()` method.

If you query for some identifier, you can use the convenience method `first()`:

```php
$record = $sru->first('alma.isbn="0415919118"');
```

The result is a [Record](//scriptotek.github.io/php-sru-client/api_docs/Scriptotek/Sru/Record.html)
object, or `null` if not found.


## Use explain to get information about servers

```php
$urls = array(
    'http://sru.bibsys.no/search/biblio',
    'http://lx2.loc.gov:210/LCDB',
    'http://services.d-nb.de/sru/zdb',
    'http://api.libris.kb.se/sru/libris',
);

foreach ($urls as $url) {

    $sru = new SruClient($url, [
        'version' => '1.1',
        'user-agent' => 'MyTool/0.1'
    ]);

    try {
        $response = $sru->explain();
    } catch (\Scriptotek\Sru\Exceptions\SruErrorException $e)
        print 'ERROR: ' . $e->getMessage() . "\n";
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

## API documentation

API documentation can be generated using e.g. [Sami](https://github.com/fabpot/sami),
which is included in the dev requirements of `composer.json`.

    php vendor/bin/sami.php update sami.config.php -v

You can view it at [scriptotek.github.io/php-sru-client](//scriptotek.github.io/php-sru-client/)

## Laravel 5 integration

Add the service provider to the `'providers'` array in `config/app.php`:

    Scriptotek\Sru\Providers\SruServiceProvider::class,

Optionally, add the facade to the `'aliases'` array in the same file:

    'SruClient'      => Scriptotek\Sru\Facades\SruClient::class,

To create the configuration file `config/sru.php`:

    $ php artisan vendor:publish --provider="Scriptotek\Sru\Providers\SruServiceProvider"

