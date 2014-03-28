<?php namespace Scriptotek\Tests;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;
use \Scriptotek\Sru\Response as SruResponse;

class ResponseTest extends TestCase {

    public function testParse()
    {
        $res = new SruResponse('<?xml version="1.0" encoding="UTF-8" ?>
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

        $this->assertCount(2, $res->records);
        $this->assertNull($res->error);

    }

}