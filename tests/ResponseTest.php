<?php namespace Scriptotek\Sru;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;

class ResponseTest extends TestCase {

    public function testSingleRecordResult()
    {
        $res = new Response('<?xml version="1.0" encoding="UTF-8" ?>
          <srw:searchRetrieveResponse 
            xmlns:srw="http://www.loc.gov/zing/srw/" 
            xmlns:xcql="http://www.loc.gov/zing/cql/xcql/"
          >
            <srw:version>1.1</srw:version>
            <srw:numberOfRecords>1</srw:numberOfRecords>
            <srw:records>
              <srw:record>
                <srw:recordSchema>marcxchange</srw:recordSchema>
                <srw:recordPacking>xml</srw:recordPacking>
                <srw:recordPosition>1</srw:recordPosition>
                <srw:recordData>Record 1</srw:recordData>
              </srw:record>
            </srw:records>
            <srw:echoedSearchRetrieveRequest>
              <srw:operation>searchRetrieve</srw:operation>
              <srw:version>1.1</srw:version>
              <srw:query>bs.avdelingsamling = &quot;urealastr&quot; AND bs.lokal-klass = &quot;k C11?&quot;</srw:query>
              <srw:startRecord>1</srw:startRecord>
              <srw:maximumRecords>2</srw:maximumRecords>
              <srw:recordSchema>marcxchange</srw:recordSchema>
            </srw:echoedSearchRetrieveRequest>
            <srw:extraResponseData>
              <responseDate>2014-03-28T12:09:50Z</responseDate>
            </srw:extraResponseData>
          </srw:searchRetrieveResponse>');

        $this->assertNull($res->error);
        $this->assertEquals('1.1', $res->version);
        $this->assertEquals(1, $res->numberOfRecords);
        $this->assertNull($res->nextRecordPosition);

        $this->assertCount(1, $res->records);
        $this->assertEquals(1, $res->records[0]->position);
        $this->assertEquals('marcxchange', $res->records[0]->schema);
        $this->assertEquals('xml', $res->records[0]->packing);
        $this->assertEquals('Record 1', $res->records[0]->data);
    }

    public function testMultipleRecordsResult()
    {
        $res = new Response('<?xml version="1.0" encoding="UTF-8" ?>
          <srw:searchRetrieveResponse 
            xmlns:srw="http://www.loc.gov/zing/srw/" 
            xmlns:xcql="http://www.loc.gov/zing/cql/xcql/"
          >
            <srw:version>1.1</srw:version>
            <srw:numberOfRecords>303</srw:numberOfRecords>
            <srw:records>
              <srw:record>
                <srw:recordSchema>marcxchange</srw:recordSchema>
                <srw:recordPacking>xml</srw:recordPacking>
                <srw:recordPosition>1</srw:recordPosition>
                <srw:recordData>Record 1</srw:recordData>
              </srw:record>
              <srw:record>
                <srw:recordSchema>marcxchange</srw:recordSchema>
                <srw:recordPacking>xml</srw:recordPacking>
                <srw:recordPosition>2</srw:recordPosition>
                <srw:recordData>Record 2</srw:recordData>
              </srw:record>
            </srw:records>
            <srw:nextRecordPosition>3</srw:nextRecordPosition>
            <srw:echoedSearchRetrieveRequest>
              <srw:operation>searchRetrieve</srw:operation>
              <srw:version>1.1</srw:version>
              <srw:query>bs.avdelingsamling = &quot;urealastr&quot; AND bs.lokal-klass = &quot;k C11?&quot;</srw:query>
              <srw:startRecord>1</srw:startRecord>
              <srw:maximumRecords>2</srw:maximumRecords>
              <srw:recordSchema>marcxchange</srw:recordSchema>
            </srw:echoedSearchRetrieveRequest>
            <srw:extraResponseData>
              <responseDate>2014-03-28T12:09:50Z</responseDate>
            </srw:extraResponseData>
          </srw:searchRetrieveResponse>');

        $this->assertNull($res->error);
        $this->assertEquals('1.1', $res->version);
        $this->assertEquals(303, $res->numberOfRecords);
        $this->assertEquals(3, $res->nextRecordPosition);

        $this->assertCount(2, $res->records);
        $this->assertEquals(1, $res->records[0]->position);
        $this->assertEquals('marcxchange', $res->records[0]->schema);
        $this->assertEquals('xml', $res->records[0]->packing);
        $this->assertEquals('Record 1', $res->records[0]->data);
    }

    public function testError()
    {
      $res = new Response('<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/">
          <srw:version>1.1</srw:version>
          <srw:numberOfRecords>0</srw:numberOfRecords>
          <srw:diagnostics xmlns="http://www.loc.gov/zing/srw/diagnostic/">
            <diagnostic >
              <uri>info:srw/diagnostic/1/66</uri>
              <details>Invalid parameter: \'marcxml\' for service: \'biblio\'</details>
              <message>Unknown schema for retrieval</message>
            </diagnostic>
          </srw:diagnostics>
        </srw:searchRetrieveResponse>');

      $this->assertEquals('Unknown schema for retrieval. Invalid parameter: \'marcxml\' for service: \'biblio\'', $res->error);
    }

}