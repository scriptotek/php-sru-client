<?php

namespace Scriptotek\Sru;

/**
 * Interface defining data objects that hold the information of an SRU response
 */
interface ResponseInterface
{
    /**
     * Create a new response
     *
     * @param string|null $text Raw XML response
     * @param Client|null $client SRU client reference (optional)
     * @return void
     */
    public function __construct(string $text = null, Client &$client = null);

    /**
     * Get the raw xml response
     *
     * @return string
     */
    public function asXml(): string;
}
