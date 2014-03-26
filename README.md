## php-sru-client

Simple PHP Search/Retrieve via URL (SRU) class using [Guzzle](http://guzzlephp.org/)
and returns [QuiteSimpleXMLElement](//github.com/danmichaelo/quitesimplexmlelement) objects.

If you prefer a simple text response instead of a QuiteSimpleXmlElement object, you might try 
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
use Scriptotek\SruClient;

$url = 'http://sru.bibsys.no/search/biblioholdings';

$client = new SruClient($url, array(
    'schema' => 'marcxml',
    'version' => '1.1',
    'user-agent' => 'OpenKat/0.1'
);

$response = $client->search('dc.title="Hello world"');

```
