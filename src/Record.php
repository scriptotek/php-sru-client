<?php namespace Scriptotek\Sru;

use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

/**
 * Single record from a SRU response
 */
class Record
{
    /** @var int */
    public $position;

    /** @var string */
    public $packing;

    /** @var string */
    public $schema;

    /** @var QuiteSimpleXMLElement */
    public $data;

    static $recordTpl = '<srw:record xmlns:srw="http://www.loc.gov/zing/srw/">
            <srw:recordSchema>{{recordSchema}}</srw:recordSchema>
            <srw:recordPacking>{{recordPacking}}</srw:recordPacking>
            <srw:recordPosition>{{position}}</srw:recordPosition>
            <srw:recordData>{{data}}</srw:recordData>
          </srw:record>';

    /**
     * Create a new record
     * @param QuiteSimpleXMLElement $doc
     */
    public function __construct(QuiteSimpleXMLElement $doc)
    {
        $this->position = intval($doc->text('./srw:recordPosition'));
        $this->packing = $doc->text('./srw:recordPacking');
        $this->schema = $doc->text('./srw:recordSchema');
        $this->data = $doc->first('./srw:recordData');
    }

    /**
     * @param int $position
     * @param string|QuiteSimpleXMLElement $data
     * @param string $recordSchema
     * @param string $recordPacking
     * @return Record
     */
    public static function make($position, $data, $recordSchema='marcxchange', $recordPacking='xml')
    {
        $record = str_replace(
            array('{{position}}', '{{data}}', '{{recordSchema}}', '{{recordPacking}}'),
            array($position, $data, $recordSchema, $recordPacking),
            self::$recordTpl
        );

        return new Record(new QuiteSimpleXMLElement($record));
    }
}
