<?php namespace Scriptotek\Sru;
 
use \Guzzle\Http\Client as HttpClient;

/**
 * SRU client
 */
class Client {

    /** @var HttpClient */
    protected $httpClient;

    /** @var string SRU service base URL */
    protected $url;

    /** @var string Requested schema for the returned records */
    protected $schema;

    /** @var string SRU protocol version */
    protected $version;

    /** @var string Some user agent string to identify our client */
    protected $userAgent;

    /**
     * @var string|string[] Proxy configuration details.
     *
     * Either a string 'host:port' or an 
     * array('host:port', 'username', 'password').
     */
    protected $proxy;

    /**
     * @var string[] Array containing username and password
     */
    protected $credentials;

	/**
	 * Create a new client
	 *
	 * @param string $url Base URL to the SRU service
	 * @param array $options Associative array of options
	 * @param HttpClient $httpClient
	 */
    public function __construct($url, $options = null, $httpClient = null)
    {
        $this->url = $url;
        $options = $options ?: array();
        $this->httpClient = $httpClient ?: new HttpClient;

        $this->schema = isset($options['schema'])
            ? $options['schema']
            : 'marcxml';

        $this->namespaces = isset($options['namespaces'])
            ? $options['namespaces']
            : array(
                'srw' => 'http://www.loc.gov/zing/srw/',
                'marc' => 'http://www.loc.gov/MARC21/slim',
                'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
            );

        $this->version = isset($options['version'])
            ? $options['version']
            : '1.1';

        $this->userAgent = isset($options['user-agent'])
            ? $options['user-agent']
            : null;

        $this->credentials = isset($options['credentials'])
            ? $options['credentials']
            : null;

        $this->proxy = isset($options['proxy'])
            ? $options['proxy']
            : null;
    }

    /**
     * Construct the URL for a CQL query
     *
     * @param string $cql The CQL query
     * @param int $start Start value in result set (optional)
     * @param int $count Number of records to request (optional)
     * @return string
     */
    public function urlTo($cql, $start = 1, $count = 10)
    {
        $qs = array(
            'version' => $this->version,
            'operation' => 'searchRetrieve',
            'recordSchema' => $this->schema,
            'maximumRecords' => $count,
            'query' => $cql
        );

        if ($start != 1) {
            // At least the BIBSYS SRU service, specifying startRecord results in 
            // a less clear error message when there's no results
            $qs['startRecord'] = $start;
        }

        return $this->url . '?' . http_build_query($qs);
    }

    /**
     * Perform a searchRetrieveResponse request
     * 
     * @param string $cql
     * @param int $start Start value in result set (optional)
     * @param int $count Number of records to request (optional)
     * @return Response
     */
    public function search($cql, $start = 1, $count = 10) {

        $url = $this->urlTo($cql, $start, $count);
        $headers = array(
            'Accept' => 'application/xml'
        );
        if ($this->userAgent) {
            $headers['User-Agent'] = $this->userAgent;
        }
        $options = array(
            'headers' => $headers
        );
        if ($this->credentials) {
            $options['auth'] = $this->credentials;
        }
        if ($this->proxy) {
            $options['proxy'] = $this->proxy;
        }

        $res = $this->httpClient->get($url, $options)->send();
        $body = $res->getBody(true);

        return new Response($body, $this->namespaces);

    }

}

