<?php namespace Scriptotek\Sru;

/**
 * Interface defining data objects that hold the information of an SRU response
 */
interface ResponseInterface
{
    /**
     * Create a new response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     * @return void
     */
    public function __construct($text, &$client = null);

    /**
     * Get the raw xml response
     *
     * @return string
     */
    public function asXml();
}
