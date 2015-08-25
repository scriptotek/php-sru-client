<?php namespace Scriptotek\Sru;

use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * Generic SRU response
 */
class Response implements ResponseInterface
{
    public static $errorMessages = array(
        'info:srw/diagnostic/1/1' => 'General system error',
        'info:srw/diagnostic/1/2' => 'System temporarily unavailable',
        'info:srw/diagnostic/1/3' => 'Authentication error',
        'info:srw/diagnostic/1/4' => 'Unsupported operation',
        'info:srw/diagnostic/1/5' => 'Unsupported version',
        'info:srw/diagnostic/1/6' => 'Unsupported parameter value',
        'info:srw/diagnostic/1/7' => 'Mandatory parameter not supplied',
        'info:srw/diagnostic/1/8' => 'Unsupported parameter',
        'info:srw/diagnostic/1/10' => 'Query syntax error',
        'info:srw/diagnostic/1/12' => 'Too many characters in query',
        'info:srw/diagnostic/1/13' => 'Invalid or unsupported use of parentheses',
        'info:srw/diagnostic/1/14' => 'Invalid or unsupported use of quotes',
        'info:srw/diagnostic/1/15' => 'Unsupported context set',
        'info:srw/diagnostic/1/16' => 'Unsupported index',
        'info:srw/diagnostic/1/18' => 'Unsupported combination of indexes',
        'info:srw/diagnostic/1/19' => 'Unsupported relation',
        'info:srw/diagnostic/1/20' => 'Unsupported relation modifier',
        'info:srw/diagnostic/1/21' => 'Unsupported combination of relation modifers',
        'info:srw/diagnostic/1/22' => 'Unsupported combination of relation and index',
        'info:srw/diagnostic/1/23' => 'Too many characters in term',
        'info:srw/diagnostic/1/24' => 'Unsupported combination of relation and term',
        'info:srw/diagnostic/1/26' => 'Non special character escaped in term',
        'info:srw/diagnostic/1/27' => 'Empty term unsupported',
        'info:srw/diagnostic/1/28' => 'Masking character not supported',
        'info:srw/diagnostic/1/29' => 'Masked words too short',
        'info:srw/diagnostic/1/30' => 'Too many masking characters in term',
        'info:srw/diagnostic/1/31' => 'Anchoring character not supported',
        'info:srw/diagnostic/1/32' => 'Anchoring character in unsupported position',
        'info:srw/diagnostic/1/33' => 'Combination of proximity/adjacency and masking characters not supported',
        'info:srw/diagnostic/1/34' => 'Combination of proximity/adjacency and anchoring characters not supported',
        'info:srw/diagnostic/1/35' => 'Term contains only stopwords',
        'info:srw/diagnostic/1/36' => 'Term in invalid format for index or relatio',
        'info:srw/diagnostic/1/37' => 'Unsupported boolean operator',
        'info:srw/diagnostic/1/38' => 'Too many boolean operators in query',
        'info:srw/diagnostic/1/39' => 'Proximity not supported',
        'info:srw/diagnostic/1/40' => 'Unsupported proximity relation',
        'info:srw/diagnostic/1/41' => 'Unsupported proximity distance',
        'info:srw/diagnostic/1/42' => 'Unsupported proximity unit',
        'info:srw/diagnostic/1/43' => 'Unsupported proximity ordering',
        'info:srw/diagnostic/1/44' => 'Unsupported combination of proximity modifiers',
        'info:srw/diagnostic/1/46' => 'Unsupported boolean modifier',
        'info:srw/diagnostic/1/47' => 'Cannot process query; reason unknown',
        'info:srw/diagnostic/1/48' => 'Query feature unsupported',
        'info:srw/diagnostic/1/49' => 'Masking character in unsupported position',
        'info:srw/diagnostic/1/50' => 'Result sets not supported',
        'info:srw/diagnostic/1/51' => 'Result set does not exist',
        'info:srw/diagnostic/1/52' => 'Result set temporarily unavailable',
        'info:srw/diagnostic/1/53' => 'Result sets only supported for retrieval',
        'info:srw/diagnostic/1/55' => 'Combination of result sets with search terms not supported',
        'info:srw/diagnostic/1/58' => 'Result set created with unpredictable partial results available',
        'info:srw/diagnostic/1/59' => 'Result set created with valid partial results available',
        'info:srw/diagnostic/1/60' => 'Result set not created: too many matching records',
        'info:srw/diagnostic/1/61' => 'First record position out of range',
        'info:srw/diagnostic/1/64' => 'Record temporarily unavailable',
        'info:srw/diagnostic/1/65' => 'Record does not exist',
        'info:srw/diagnostic/1/66' => 'Unknown schema for retrieval',
        'info:srw/diagnostic/1/67' => 'Record not available in this schema',
        'info:srw/diagnostic/1/68' => 'Not authorised to send record',
        'info:srw/diagnostic/1/69' => 'Not authorised to send record in this schema',
        'info:srw/diagnostic/1/70' => 'Record too large to send',
        'info:srw/diagnostic/1/71' => 'Unsupported record packing',
        'info:srw/diagnostic/1/72' => 'XPath retrieval unsupported',
        'info:srw/diagnostic/1/73' => 'XPath expression contains unsupported feature',
        'info:srw/diagnostic/1/74' => 'Unable to evaluate XPath expression',
        'info:srw/diagnostic/1/80' => 'Sort not supported',
        'info:srw/diagnostic/1/82' => 'Unsupported sort sequence',
        'info:srw/diagnostic/1/83' => 'Too many records to sort',
        'info:srw/diagnostic/1/84' => 'Too many sort keys to sort',
        'info:srw/diagnostic/1/86' => 'Cannot sort: incompatible record formats',
        'info:srw/diagnostic/1/87' => 'Unsupported schema for sort',
        'info:srw/diagnostic/1/88' => 'Unsupported path for sort',
        'info:srw/diagnostic/1/89' => 'Path unsupported for schema',
        'info:srw/diagnostic/1/90' => 'Unsupported direction',
        'info:srw/diagnostic/1/91' => 'Unsupported case',
        'info:srw/diagnostic/1/92' => 'Unsupported missing value action',
        'info:srw/diagnostic/1/93' => 'Sort ended due to missing value',
        'info:srw/diagnostic/1/94' => 'Sort spec included both in query and protocol: query prevails',
        'info:srw/diagnostic/1/95' => 'Sort spec included both in query and protocol: protocol prevails',
        'info:srw/diagnostic/1/96' => 'Sort spec included both in query and protocol: error',
        'info:srw/diagnostic/1/110' => 'Stylesheets not supported',
        'info:srw/diagnostic/1/120' => 'Response position out of range',
        'info:srw/diagnostic/1/130' => 'Too many terms matched by masked query term',
    );

    /** @var string Raw XML response */
    protected $rawResponse;

    /** @var QuiteSimpleXMLElement XML response */
    protected $response;

    /** @var Client Reference to SRU client object */
    protected $client;

    /** @var string Error message */
    public $error;

    /** @var string SRU protocol version */
    public $version;

    /**
     * Create a new response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     */
    public function __construct($text, &$client = null)
    {
        $this->rawResponse = $text;

        // Throws Danmichaelo\QuiteSimpleXMLElement\InvalidXMLException on invalid xml
        $this->response = new QuiteSimpleXMLElement($text);

        $this->client = $client;

        $this->response->registerXPathNamespaces(array(
            'srw' => 'http://www.loc.gov/zing/srw/',
            'exp' => 'http://explain.z3950.org/dtd/2.0/',
            'd' => 'http://www.loc.gov/zing/srw/diagnostic/'
        ));

        $this->version = $this->response->text('/srw:*/srw:version');

        $e = $this->response->first('/srw:*/srw:diagnostics');
        if ($e) {
            // Only the 'uri' field is required, 'message' and 'details' are optional
            $uri = $e->text('d:diagnostic/d:uri');
            $msg = $e->text('d:diagnostic/d:message');
            $details = $e->text('d:diagnostic/d:details');
            if (empty($msg)) {
                if (isset(self::$errorMessages[$uri])) {
                    $msg = self::$errorMessages[$uri];
                } else {
                    $msg = 'Unknown error';
                }
            }
            if (!empty($details)) {
                $msg .= ' (' . $details . ')';
            }
            $this->error = $msg;
        }
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
