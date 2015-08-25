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

    /**
     * Create a new record
     *
     * @param Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement $doc
     */
    public function __construct($doc)
    {
        $this->position = intval($doc->text('./srw:recordPosition'));
        $this->packing = $doc->text('./srw:recordPacking');
        $this->schema = $doc->text('./srw:recordSchema');
        $this->data = $doc->first('./srw:recordData');
    }
}
