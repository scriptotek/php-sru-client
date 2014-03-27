<?php namespace Scriptotek;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;
use \Guzzle\Http\Client as HttpClient;

class SRUClient {

    protected $httpClient;
    protected $url;
    protected $schema;
    protected $namespaces;
    protected $version;
    protected $userAgent;

    /**
     * Proxy: Either a string or an array:
     * - '127.0.0.1:3128' 
     * - array( '127.0.0.1:3128', 'my_username', 'my_password' )
     */
    protected $proxy;

    /**
     * array(username, password)
     */
    protected $credentials;

	/**
	 * Create a new client object
	 *
	 * @param string $url
	 * @param array $options Associative array of options
	 * @param Guzzle\Http\Client $httpClient
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
     * @param string $cql
     * @param int $start Start value in result set (optional)
     * @param count $count Number of records to request (optional)
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
     * @param count $count Number of records to request (optional)
     * @return QuiteSimpleXMLElement
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

        try {
            $xml = new QuiteSimpleXMLElement($body);
        } catch (\Exception $e) {
            throw new \Exception('Invalid response received from SRU service');
        }

        $xml->registerXPathNamespaces($this->namespaces);
        return $xml;

    }

}

