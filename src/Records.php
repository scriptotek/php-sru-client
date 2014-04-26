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
class Records implements \Iterator {

	/** @var HttpClient */
	protected $httpClient;

	private $position;
	private $count;
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
     * @param mixed $httpClient A http client
     */
	public function __construct($cql, Client $client, $count = 10, $httpClient = null) {
		$this->position = 1;
		$this->count = $count; // number of records per request
		$this->cql = $cql;
		$this->httpClient = $httpClient ?: new HttpClient;
		$this->client = $client;
		$this->fetchMore();
	}

	/**
     * Return error message from last reponse, if any
     *
     * @return string|null
     */
	function __get($prop) {
		if ($prop == 'error') {
			return $this->lastResponse->error;
		}
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
		$url = $this->client->urlTo($this->cql, $this->position, $this->count);
		$options = $this->client->getHttpOptions();

		$res = $this->httpClient->get($url, $options)->send();
		$body = $res->getBody(true);
		$this->lastResponse = new SearchRetrieveResponse($body);
		$this->data = $this->lastResponse->records;

		if (count($this->data) != 0 && $this->data[0]->position != $this->position) {
			throw new InvalidResponseException('Wrong index of first record in result set. ' 
				. 'Expected: ' .$this->position . ', got: ' . $this->data[0]->position
			);
		}
	}

	/**
     * Return the current element
     *
     * @return mixed
     */
	function current() {
		return $this->data[0];
	}

	/**
     * Return the key of the current element
	 *
     * @return int
     */
	function key() {
		return $this->position;
	}

	/**
     * Rewind the Iterator to the first element
     */
	function rewind() {
		if ($this->position != 1) {
			$this->position = 1;
			$this->data = array();
			$this->fetchMore();
		}
	}

	/**
     * Move forward to next element
     */
	function next() {

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
	function valid() {
		return count($this->data) != 0;
	}

}
