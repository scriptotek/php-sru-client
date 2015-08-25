<?php namespace Scriptotek\Sru;

/**
 * SearchRetrieve response, containing a list of records or some error
 */
class SearchRetrieveResponse extends Response implements ResponseInterface
{
    /** @var Record[] Array of records */
    public $records;

    /** @var int Total number of records in the result set */
    public $numberOfRecords;

    /** @var int Position of next record in the result set, or null if no such record exist */
    public $nextRecordPosition;

    /** @var string The CQL query used to generate the response */
    public $query;

    /**
     * Create a new searchRetrieve response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     */
    public function __construct($text, &$client = null)
    {
        parent::__construct($text, $client);

        $this->numberOfRecords = (int) $this->response->text('/srw:searchRetrieveResponse/srw:numberOfRecords');
        $this->nextRecordPosition = (int) $this->response->text('/srw:searchRetrieveResponse/srw:nextRecordPosition') ?: null;

        // The server may echo the request back to the client along with the response
        $this->query = $this->response->text('/srw:searchRetrieveResponse/srw:echoedSearchRetrieveRequest/srw:query') ?: null;

        $this->records = array();
        foreach ($this->response->xpath('/srw:searchRetrieveResponse/srw:records/srw:record') as $record) {
            $this->records[] = new Record($record);
        }
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
