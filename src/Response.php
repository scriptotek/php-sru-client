<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * SRU response, containing a list of records or some error
 */
class Response {

    /** @var Record[] */
    public $records;

    /** @var string */
    public $error;

    /** @var string */
    protected $rawResponse;

    /**
     * Create a new response
     *
     * @param string $text
     */
    public function __construct($text)
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
        
        $this->records = array();
        foreach ($doc->xpath('/srw:searchRetrieveResponse/srw:records/srw:record') as $record) {
            $this->records[] = $record;
        }

    }

    /**
     * Return the raw xml response
     *
     * @return string
     */
    public function asXml()
    {
        return $this->rawResponse;
    }

    public function next()
    {
        # code...
    }

}

