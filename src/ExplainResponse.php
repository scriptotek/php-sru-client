<?php namespace Scriptotek\Sru;

use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * Explain response
 */
class ExplainResponse extends Response implements ResponseInterface
{
    /** @var string Server hostname */
    public $host;

    /** @var int Server port */
    public $port;

    /** @var object Server database */
    public $database;

    /** @var array Server indexes */
    public $indexes;

    /**
     * Create a new explain response
     *
     * @param string $text Raw XML response
     * @param Client $client SRU client reference (optional)
     * @param string $url Request URL
     */
    public function __construct($text = null, &$client = null, $url = null)
    {
        parent::__construct($text, $client, $url);

        $this->indexes = array();

        if (is_null($this->response)) {
            return;
        }
        $explain = $this->response->first('/srw:explainResponse/srw:record/srw:recordData/exp:explain');
        if (!$explain) {
            return;
        }

        $this->parseExplainResponse($explain);
    }

    protected function parseExplainResponse(QuiteSimpleXMLElement $node)
    {
        $serverInfo = $node->first('exp:serverInfo');
        $dbInfo = $node->first('exp:databaseInfo');
        $indexInfo = $node->first('exp:indexInfo');

        $this->host = $serverInfo->text('exp:host');
        $this->port = (int) $serverInfo->text('exp:port');
        $this->database = new \StdClass;
        $this->database->identifier = $serverInfo->text('exp:database');
        $this->database->title = $dbInfo->text('exp:title');
        $this->database->description = $dbInfo->text('exp:description');

        foreach ($indexInfo->xpath('exp:index') as $index) {
            $ind = new \StdClass;
            $ind->scan = ($index->attr('scan') == 'true');
            $ind->search = ($index->attr('search') == 'true');
            $ind->sort = ($index->attr('sort') == 'true');
            $ind->title = $index->text('exp:title');
            $ind->maps = array();
            foreach ($index->xpath('exp:map') as $map) {
                $set = $map->first('exp:name')->attr('set');
                $name = $map->text('exp:name');
                $ind->maps[] = $set . '.' . $name;
            }
            $this->indexes[] = $ind;
        }

        // TODO
    }
}
