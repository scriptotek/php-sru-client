<?php namespace Scriptotek\Sru;

use Mockery as m;

class SearchRetrieveResponseTest extends TestCase
{
    public function testSingleRecordResult()
    {
        $res = new SearchRetrieveResponse('<?xml version="1.0" encoding="UTF-8" ?>
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
        $res = new SearchRetrieveResponse('<?xml version="1.0" encoding="UTF-8" ?>
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

        $this->assertEquals('1.1', $res->version);
        $this->assertEquals(303, $res->numberOfRecords);
        $this->assertEquals(3, $res->nextRecordPosition);

        $this->assertCount(2, $res->records);
        $this->assertEquals(1, $res->records[0]->position);
        $this->assertEquals('marcxchange', $res->records[0]->schema);
        $this->assertEquals('xml', $res->records[0]->packing);
        $this->assertEquals('Record 1', $res->records[0]->data);
    }

    /**
     * @expectedException         Scriptotek\Sru\Exceptions\SruErrorException
     * @expectedExceptionMessage  Unknown schema for retrieval (Invalid parameter: 'marcxml' for service: 'biblio')
     */
    public function testErrorWithDetails()
    {
        $res = new SearchRetrieveResponse('<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/">
          <srw:version>1.1</srw:version>
          <srw:numberOfRecords>0</srw:numberOfRecords>
          <srw:diagnostics xmlns="http://www.loc.gov/zing/srw/diagnostic/">
            <diagnostic >
              <uri>info:srw/diagnostic/1/66</uri>
              <details>Invalid parameter: \'marcxml\' for service: \'biblio\'</details>
            </diagnostic>
          </srw:diagnostics>
        </srw:searchRetrieveResponse>');
    }

    /**
     * @expectedException         Scriptotek\Sru\Exceptions\SruErrorException
     * @expectedExceptionMessage  General system error
     */
    public function testErrorWithoutDetails()
    {
        $res = new SearchRetrieveResponse('<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/">
          <srw:version>1.1</srw:version>
          <srw:numberOfRecords>0</srw:numberOfRecords>
          <srw:diagnostics xmlns="http://www.loc.gov/zing/srw/diagnostic/">
            <diagnostic >
              <uri>info:srw/diagnostic/1/1</uri>
            </diagnostic>
          </srw:diagnostics>
        </srw:searchRetrieveResponse>');
    }

    /**
     * @expectedException         Scriptotek\Sru\Exceptions\SruErrorException
     * @expectedExceptionMessage  Too many boolean operators, the maximum is 10. Please try a less complex query. (10)
     */
    public function testErrorWithCustomMessage()
    {
        $res = new SearchRetrieveResponse('<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/">
          <srw:version>1.1</srw:version>
          <srw:numberOfRecords>0</srw:numberOfRecords>
          <srw:diagnostics xmlns="http://www.loc.gov/zing/srw/diagnostic/">
            <diagnostic >
              <uri>info:srw/diagnostic/1/10</uri>
              <message>Too many boolean operators, the maximum is 10. Please try a less complex query.</message>
              <details>10</details>
            </diagnostic>
          </srw:diagnostics>
        </srw:searchRetrieveResponse>');
    }

    // Should not throw error
    public function testDiagnosticsWithoutError()
    {
        $res = new SearchRetrieveResponse('<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/">
          <srw:version>1.1</srw:version>
          <srw:numberOfRecords>0</srw:numberOfRecords>
          <srw:diagnostics xmlns="http://www.loc.gov/zing/srw/diagnostic/">
          </srw:diagnostics>
        </srw:searchRetrieveResponse>');
    }

}
