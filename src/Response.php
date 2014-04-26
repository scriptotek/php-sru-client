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

    /** @var string The CQL query used to generate the response */
    public $query;

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

        $doc->registerXPathNamespaces(array(
            'srw' => 'http://www.loc.gov/zing/srw/',
            'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
        ));

        $this->version = $doc->text('/srw:searchRetrieveResponse/srw:version');

        $e = $doc->first('/srw:searchRetrieveResponse/srw:diagnostics');
        if ($e) {
            $this->error = $e->text('d:diagnostic/d:message') . '. ' . $e->text('d:diagnostic/d:details'); 
        }

        // The server may echo the request back to the client along with the response
        $this->query = $doc->text('/srw:searchRetrieveResponse/srw:echoedSearchRetrieveRequest/srw:query') ?: null;

        $this->response = $doc;

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

