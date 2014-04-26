<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * SRU response, containing a list of records or some error
 */
class Response {

    /** @var string Raw XML response */
    protected $rawResponse;

    /** @var Client Reference to SRU client object */
    protected $client;

    /** @var Record[] Array of records */
    public $records;

    /** @var string Error message */
    public $error;

    /** @var string SRU protocol version */
    public $version;

    /** @var int Total number of records in the result set */
    public $numberOfRecords;

    /** @var int Position of next record in the result set, or null if no such record exist */
    public $nextRecordPosition;

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
        try {
            $doc = new QuiteSimpleXMLElement($text);
        } catch (\Exception $e) {
            throw new \Exception('Invalid XML received');
        }

        $doc->registerXPathNamespaces(array(
            'srw' => 'http://www.loc.gov/zing/srw/',
            'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
        ));

        $this->version = $doc->text('/srw:searchRetrieveResponse/srw:version');
        $this->numberOfRecords = (int) $doc->text('/srw:searchRetrieveResponse/srw:numberOfRecords');
        $this->nextRecordPosition = (int) $doc->text('/srw:searchRetrieveResponse/srw:nextRecordPosition') ?: null;

        $this->records = array();
        foreach ($doc->xpath('/srw:searchRetrieveResponse/srw:records/srw:record') as $record) {
            $this->records[] = new Record($record);
        }

        $e = $doc->first('/srw:searchRetrieveResponse/srw:diagnostics');
        if ($e) {
            $this->error = $e->text('d:diagnostic/d:message') . '. ' . $e->text('d:diagnostic/d:details'); 
        }

        $this->client = $client;

        // The server may echo the request back to the client along with the response
        $this->query = $doc->text('/srw:searchRetrieveResponse/srw:echoedSearchRetrieveRequest/srw:query') ?: null;

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

    /**
     * Request next batch of records in the result set, or return null if we're at the end of the set
     *
     * @return Response
     */
    public function next()
    {
        if (is_null($this->client)) {
            throw new \Exception('No client reference passed to response');
        }
        if (is_null($this->query)) {
            throw new \Exception('No query available');
        }
        if (is_null($this->nextRecordPosition)) {
            return null;
        }
        return $this->client->search($this->query, $this->nextRecordPosition, count($this->records));
    }

}

