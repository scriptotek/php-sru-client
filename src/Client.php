<?php namespace Scriptotek\Sru;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Client\Common\Exception\ServerErrorException;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;
use Http\Message\MessageFactory;

/**
 * SRU client
 */
class Client
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var MessageFactory */
    protected $messageFactory;

    /** @var string SRU service base URL */
    protected $url;

    /** @var string Requested schema for the returned records */
    protected $schema;

    /** @var string SRU protocol version */
    protected $version;

    /** @var string Some user agent string to identify our client */
    protected $userAgent;

    /** @var array Custom headers */
    public $headers;

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
     * @param string              $url     Base URL to the SRU service
     * @param array               $options Associative array of options
     * @param HttpClient          $httpClient
     * @param MessageFactory|null $messageFactory
     * @throws \ErrorException
     */
    public function __construct(
        $url,
        $options = null,
        HttpClient $httpClient = null,
        MessageFactory $messageFactory = null
    ) {
        $this->url = $url;
        $options = $options ?: array();

        $plugins = [];

        $this->schema = isset($options['schema'])
            ? $options['schema']
            : 'marcxml';

        $this->version = isset($options['version'])
            ? $options['version']
            : '1.1';

        $this->headers = isset($options['headers'])
            ? $options['headers']
            : ['Accept' => 'application/xml'];

        if (isset($options['user-agent'])) {
            // legacy option
            $this->headers['User-Agent'] = $options['user-agent'];
        }

        if (isset($options['credentials'])) {
            $authentication = new BasicAuth($options['credentials'][0], $options['credentials'][1]);
            $plugins[] = new AuthenticationPlugin($authentication);
        }

        if (isset($options['proxy'])) {
            throw new\ErrorException('Not supported');
        }

        $this->httpClient = new PluginClient($httpClient ?: HttpClientDiscovery::find(), $plugins);
        $this->messageFactory = $messageFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * Construct the URL for a CQL query
     *
     * @param string $cql The CQL query
     * @param int $start Start value in result set (optional)
     * @param int $count Number of records to request (optional)
     * @param array $extraParams Extra GET parameters
     * @return string
     */
    public function urlTo($cql, $start = 1, $count = 10, $extraParams = array())
    {
        $qs = array(
            'operation' => 'searchRetrieve',
            'version' => $this->version,
            'recordSchema' => $this->schema,
            'maximumRecords' => $count,
            'query' => $cql
        );

        if ($start != 1) {
            // At least the BIBSYS SRU service, specifying startRecord results in
            // a less clear error message when there's no results
            $qs['startRecord'] = $start;
        }

        foreach ($extraParams as $key => $value) {
            $qs[$key] = $value;
        }

        return $this->url . '?' . http_build_query($qs);
    }

    /**
     * Perform a searchRetrieve request
     *
     * @deprecated
     * @param string $cql
     * @param int $start Start value in result set (optional)
     * @param int $count Number of records to request (optional)
     * @param array $extraParams Extra GET parameters
     * @return SearchRetrieveResponse
     */
    public function search($cql, $start = 1, $count = 10, $extraParams = array())
    {
        $url = $this->urlTo($cql, $start, $count, $extraParams);
        $body = $this->request('GET', $url);

        return new SearchRetrieveResponse($body, $this);
    }

    /**
     * Perform a searchRetrieve request and return an iterator over the records
     *
     * @param string $cql
     * @param int $batchSize Number of records to request per request
     * @param array $extraParams Extra GET parameters
     * @return Records
     */
    public function all($cql, $batchSize = 10, $extraParams = array())
    {
        return new Records($cql, $this, $batchSize, $extraParams);
    }

    /**
     * Alias for `all()`
     * @deprecated
     * @param $cql
     * @param int $batchSize
     * @param array $extraParams
     * @return Records
     */
    public function records($cql, $batchSize = 10, $extraParams = array())
    {
        return $this->all($cql, $batchSize, $extraParams);
    }

    /**
     * Perform a searchRetrieve request and return first record
     *
     * @param string $cql
     * @param array $extraParams Extra GET parameters
     * @return Record
     */
    public function first($cql, $extraParams = array())
    {
        $recs = new Records($cql, $this, 1, $extraParams);
        return $recs->numberOfRecords() ? $recs->current() : null;
    }

    /**
     * Perform an explain request
     *
     * @return ExplainResponse
     */
    public function explain()
    {
        $url = $this->url . '?' . http_build_query(array(
            'operation' => 'explain',
            'version' => $this->version,
        ));

        $body = $this->request('GET', $url);

        return new ExplainResponse($body, $this);
    }

    /**
     * @param string $method
     * @param string $url
     * @return string
     */
    public function request($method, $url)
    {
        $request = $this->messageFactory->createRequest($method, $url, $this->headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerErrorException($response->getReasonPhrase(), $request, $response);
        }

        return (string) $response->getBody();
    }
}
