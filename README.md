
[![Build Status](https://img.shields.io/travis/scriptotek/php-sru-client.svg)](https://travis-ci.org/scriptotek/php-sru-client)
[![Coverage Status](https://img.shields.io/coveralls/scriptotek/php-sru-client.svg)](https://coveralls.io/r/scriptotek/php-sru-client?branch=master)

## php-sru-client

A simple PHP class for making [Search/Retrieve via URL](http://www.loc.gov/standards/sru/) (SRU) requests,using the 
[Guzzle HTTP client](http://guzzlephp.org/)
and returning 
[QuiteSimpleXMLElement](//github.com/danmichaelo/quitesimplexmlelement) instances.

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

### Example

```php
require_once('vendor/autoload.php');
use Scriptotek\Sru\Client as SruClient;

$url = 'http://sru.bibsys.no/search/biblioholdings';

$client = new SruClient($url, array(
    'schema' => 'marcxml',
    'version' => '1.1',
    'user-agent' => 'OpenKat/0.1'
));

$records = $client->records('dc.title="Hello world"');
foreach ($records as $record) {
	echo "Got record " . $record->position . " of " . $records->numberOfRecords() . "\n";
	// processRecord($record->data);
}
```

### API documentation 

API documentation can be generated using e.g. [Sami](https://github.com/fabpot/sami),
which is included in the dev requirements of `composer.json`.

    php vendor/bin/sami.php update sami.config.php -v

You can view it at [scriptotek.github.io/php-sru-client](//scriptotek.github.io/php-sru-client/)
