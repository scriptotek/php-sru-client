<?php

namespace Scriptotek\Sru;

/**
 * SearchRetrieve response, containing a list of records or some error
 */
class SearchRetrieveResponse extends Response implements ResponseInterface
{
    /** @var Record[] Array of records */
    public array $records = [];

    /** @var int Total number of records in the result set */
    public int $numberOfRecords = 0;

    /** @var int|null Position of next record in the result set, or null if no such record exist */
    public ?int $nextRecordPosition = null;

    /** @var string|null The CQL query used to generate the response */
    public ?string $query = '';

    /**
     * Create a new searchRetrieve response
     *
     * @param string|null $text Raw XML response
     * @param Client|null $client SRU client reference (optional)
     * @param string|null $url
     */
    public function __construct(string $text = null, Client &$client = null, string $url = null)
    {
        parent::__construct($text, $client, $url);

        if (is_null($this->response)) {
            return;
        }

        $this->numberOfRecords = (int) $this->response->text('/srw:searchRetrieveResponse/srw:numberOfRecords');
        $this->nextRecordPosition = (int) $this->response->text('/srw:searchRetrieveResponse/srw:nextRecordPosition') ?: null;

        // The server may echo the request back to the client along with the response
        $this->query = $this->response->text('/srw:searchRetrieveResponse/srw:echoedSearchRetrieveRequest/srw:query') ?: null;

        $this->records = [];
        foreach ($this->response->xpath('/srw:searchRetrieveResponse/srw:records/srw:record') as $record) {
            $this->records[] = new Record($record);
        }
    }

    /**
     * Request next batch of records in the result set, or return null if we're at the end of the set
     *
     * @return Response|null
     */
    public function next(): ?Response
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
