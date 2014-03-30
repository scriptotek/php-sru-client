<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * SRU explain response
 */
class ExplainResponse {

    /** @var string Raw XML response */
    protected $rawResponse;

    /**
     * Create a new explain response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     */
    public function __construct($text, &$client = null)
    {
        $this->rawResponse = $text; 
        try {
            $doc = new QuiteSimpleXMLElement($text);
        } catch (\Exception $e) {
            throw new \Exception('Invalid XML received');
        }

        $doc->registerXPathNamespaces(array(
            'srw' => 'http://www.loc.gov/zing/srw/',
            'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
        ));

        $this->client = $client;
    }

    /**
     * Get the raw xml response
     *
     * @return string
     */
    public function asXml()
    {
        return $this->rawResponse;
    }

}

