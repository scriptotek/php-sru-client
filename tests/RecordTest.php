<?php namespace Scriptotek\Sru;

class RecordTest extends TestCase
{
	public function testMake() {
		$record = Record::make(29, 'Hello world');

		$this->assertInstanceOf('Scriptotek\Sru\Record', $record);
		$this->assertEquals(29, $record->position);
	}

	public function testToString() {
		$record = Record::make(29, 'Hello world');

        $this->assertEquals('Hello world', (string) $record);
	}

	public function testXmlToString() {
		$record = Record::make(29, '<hello>world</hello>');

        $this->assertEquals('<hello>world</hello>', (string) $record);
	}

	public function testNamespacedXmlToString() {
		$record = Record::make(29, '<c:test xmlns:c="http://www.loc.gov/zing/cql/xcql/">Test</c:test>');

        $this->assertEquals('<c:test xmlns:c="http://www.loc.gov/zing/cql/xcql/">Test</c:test>', (string) $record);
	}
}
