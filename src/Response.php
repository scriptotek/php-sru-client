<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * Generic SRU response
 */
class Response implements ResponseInterface {

    /** @var string Raw XML response */
    protected $rawResponse;

    /** @var QuiteSimpleXMLElement XML response */
    protected $response;

    /** @var Client Reference to SRU client object */
    protected $client;

    /** @var string Error message */
    public $error;

    /** @var string SRU protocol version */
    public $version;

    /**
     * Create a new response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     */
    public function __construct($text, &$client = null)
    {
        $this->rawResponse = $text;

        // Throws Danmichaelo\QuiteSimpleXMLElement\InvalidXMLException on invalid xml
        $this->response = new QuiteSimpleXMLElement($text);

        $this->client = $client;

        $this->response->registerXPathNamespaces(array(
            'srw' => 'http://www.loc.gov/zing/srw/',
            'exp' => 'http://explain.z3950.org/dtd/2.0/',
            'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
        ));

        $this->version = $this->response->text('/srw:*/srw:version');

        $e = $this->response->first('/srw:*/srw:diagnostics');
        if ($e) {
            $this->error = $e->text('d:diagnostic/d:message') . '. ' . $e->text('d:diagnostic/d:details'); 
        }
    }

    /**
     * Get the raw xml response
     *
     * @return string
     */
    public function asXml()
    {
        return $this->rawResponse;
    }

}

