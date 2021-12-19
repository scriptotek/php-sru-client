<?php namespace Scriptotek\Sru;

/**
 * When iterating, methods are called in the following order:
 *
 * rewind()
 * valid()
 * current()
 *
 * next()
 * valid()
 * current()
 *
 * ...
 *
 * next()
 * valid()
 */
class Records implements \Iterator
{
    private int $position;
    private int $count;
    private array $extraParams;
    private string $cql;
    private Client $client;
    private SearchRetrieveResponse $lastResponse;
    private array $data;

    /**
     * Create a new records iterator
     *
     * @param string $cql Query
     * @param Client $client SRU client reference (optional)
     * @param int $count Number of records to request per request
     * @param array $extraParams Extra GET parameters
     */
    public function __construct(string $cql, Client $client, int $count = 10, array $extraParams = [])
    {
        $this->position = 1;
        $this->data = [];
        $this->count = $count; // number of records per request
        $this->extraParams = $extraParams;
        $this->cql = $cql;
        $this->client = $client;
        $this->fetchMore();
    }

    /**
     * Return the number of records
     */
    public function numberOfRecords(): int
    {
        return $this->lastResponse->numberOfRecords;
    }

    /**
     * Fetch more records from the service
     */
    private function fetchMore(): void
    {
        $url = $this->client->urlTo($this->cql, $this->position, $this->count, $this->extraParams);
        $body = $this->client->request('GET', $url);
        $this->lastResponse = new SearchRetrieveResponse($body);
        $this->data = $this->lastResponse->records;

        if (count($this->data) != 0 && $this->data[0]->position != $this->position) {
            throw new Exceptions\InvalidResponseException('Wrong index of first record in result set. '
                . 'Expected: ' .$this->position . ', got: ' . $this->data[0]->position
            );
        }
    }

    /**
     * Return the current element
     */
    public function current(): Record
    {
        return $this->data[0];
    }

    /**
     * Return the key of the current element
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind(): void
    {
        if ($this->position != 1) {
            $this->position = 1;
            $this->data = [];
            $this->fetchMore();
        }
    }

    /**
     * Move forward to next element
     */
    public function next(): void
    {
        if (count($this->data) > 0) {
            array_shift($this->data);
        }
        ++$this->position;

        if ($this->position > $this->numberOfRecords()) {
            return;
        }

        if (count($this->data) == 0) {
            $this->fetchMore();
        }
    }

    /**
     * Check if current position is valid
     */
    public function valid(): bool
    {
        return count($this->data) != 0;
    }
}
