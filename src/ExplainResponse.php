<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * Explain response
 */
class ExplainResponse extends Response implements ResponseInterface {

    /**
     * Create a new explain response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     */
    public function __construct($text, &$client = null)
    {
        parent::__construct($text, $client);

        // TODO

    }

}

