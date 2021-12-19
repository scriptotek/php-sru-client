<?php namespace Scriptotek\Sru;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Exception\ServerErrorException;
use Http\Factory\Discovery\HttpClient;
use Http\Factory\Discovery\HttpFactory;
use Http\Message\Authentication\BasicAuth;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * SRU client
 */
class Client
{
    /** @var ClientInterface */
    protected $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

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
     * @param string                   $url             Base URL to the SRU service
     * @param ?array                   $options         Associative array of options
     * @param ?ClientInterface         $httpClient
     * @param ?RequestFactoryInterface $requestFactory
     * @throws \ErrorException
     */
    public function __construct(
        string $url,
        ?array $options = null,
        ClientInterface $httpClient = null,
        RequestFactoryInterface $requestFactory = null
    ) {
        $this->url = $url;
        $options = $options ?: [];

        $plugins = [];

        $this->schema = $options['schema'] ?? 'marcxml';

        $this->version = $options['version'] ?? '1.1';

        $this->headers = $options['headers'] ?? ['Accept' => 'application/xml'];

        if (isset($options['user-agent'])) {
            // legacy option
            $this->headers['User-Agent'] = $options['user-agent'];
        }

        if (isset($options['credentials'])) {
            $authentication = new BasicAuth($options['credentials'][0], $options['credentials'][1]);
            $plugins[] = new AuthenticationPlugin($authentication);
        }

        if (isset($options['proxy'])) {
            throw new \ErrorException('Not supported');
        }

        $this->httpClient = new PluginClient($httpClient ?: HttpClient::client(), $plugins);
        $this->requestFactory = $requestFactory ?: HttpFactory::requestFactory();
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
    public function urlTo(string $cql, int $start = 1, int $count = 10, array $extraParams = []): string
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
     * @param string $cql
     * @param int $start Start value in result set (optional)
     * @param int $count Number of records to request (optional)
     * @param array $extraParams Extra GET parameters
     * @return SearchRetrieveResponse
     *@deprecated
     */
    public function search(string $cql, int $start = 1, int $count = 10, array $extraParams = []): SearchRetrieveResponse
    {
        $url = $this->urlTo($cql, $start, $count, $extraParams);
        $body = $this->request('GET', $url);

        return new SearchRetrieveResponse($body, $this, $url);
    }

    /**
     * Perform a searchRetrieve request and return an iterator over the records
     *
     * @param string $cql
     * @param int $batchSize Number of records to request per request
     * @param array $extraParams Extra GET parameters
     * @return Records
     */
    public function all(string $cql, int $batchSize = 10, array $extraParams = []): Records
    {
        return new Records($cql, $this, $batchSize, $extraParams);
    }

    /**
     * Alias for `all()`
     * @param string $cql
     * @param int $batchSize
     * @param array $extraParams
     * @return Records
     *@deprecated
     */
    public function records(string $cql, int $batchSize = 10, array $extraParams = []): Records
    {
        return $this->all($cql, $batchSize, $extraParams);
    }

    /**
     * Perform a searchRetrieve request and return first record
     *
     * @param string $cql
     * @param array $extraParams Extra GET parameters
     * @return ?Record
     */
    public function first(string $cql, array $extraParams = []): ?Record
    {
        $recs = new Records($cql, $this, 1, $extraParams);
        return $recs->numberOfRecords() ? $recs->current() : null;
    }

    /**
     * Perform an explain request
     *
     * @return ExplainResponse
     */
    public function explain(): ExplainResponse
    {
        $url = $this->url . '?' . http_build_query(array(
            'operation' => 'explain',
            'version' => $this->version,
        ));

        $body = $this->request('GET', $url);

        return new ExplainResponse($body, $this, $url);
    }

    /**
     * @param string $method
     * @param string $url
     * @return string
     */
    public function request(string $method, string $url): string
    {
        $request = $this->requestFactory->createRequest($method, $url, $this->headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerErrorException($response->getReasonPhrase(), $request, $response);
        }

        return (string) $response->getBody();
    }
}
