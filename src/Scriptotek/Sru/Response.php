<?php namespace Scriptotek\Sru;
 
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

class Response {

    public $records;
    public $error;

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

        //doc->xpath('/srw:searchRetrieveResponse/

    }

    public function asXml()
    {
        return $this->rawResponse;
    }

    public function next()
    {
        # code...
    }

}

