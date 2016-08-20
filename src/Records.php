<?php namespace Scriptotek\Sru;

use \Guzzle\Http\Client as HttpClient;

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
    /** @var HttpClient */
    protected $httpClient;

    private $position;
    private $count;
    private $extraParams;
    private $cql;
    private $client;
    private $lastResponse;

    private $data = array();

    /**
     * Create a new records iterator
     *
     * @param string $cql Query
     * @param Client $client SRU client reference (optional)
     * @param int $count Number of records to request per request
     * @param array $extraParams Extra GET parameters
     * @param mixed $httpClient A http client
     */
    public function __construct($cql, Client $client, $count = 10, $extraParams = array(), $httpClient = null)
    {
        $this->position = 1;
        $this->count = $count; // number of records per request
        $this->extraParams = $extraParams;
        $this->cql = $cql;
        $this->httpClient = $httpClient ?: new HttpClient;
        $this->client = $client;
        $this->fetchMore();
    }

    /**
     * Return the number of records
     *
     * @return int|null
     */
    public function numberOfRecords()
    {
        return $this->lastResponse->numberOfRecords;
    }

    /**
     * Fetch more records from the service
     */
    private function fetchMore()
    {
        $url = $this->client->urlTo($this->cql, $this->position, $this->count, $this->extraParams);
        $options = $this->client->getHttpOptions();

        $response = $this->httpClient->get($url, $options);
        $body = (string) $response->getBody();
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
     *
     * @return mixed
     */
    public function current()
    {
        return $this->data[0];
    }

    /**
     * Return the key of the current element
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        if ($this->position != 1) {
            $this->position = 1;
            $this->data = array();
            $this->fetchMore();
        }
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        if (count($this->data) > 0) {
            array_shift($this->data);
        }
        ++$this->position;

        if ($this->position > $this->numberOfRecords()) {
            return null;
        }

        if (count($this->data) == 0) {
            $this->fetchMore();
        }

        if (count($this->data) == 0) {
            return null;
        }
    }

    /**
     * Check if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return count($this->data) != 0;
    }
}
