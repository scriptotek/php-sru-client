<?php namespace Scriptotek\Sru;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;

class ClientTest extends TestCase {

    protected $url = 'http://sru.my_fictive_host.net';

    protected $simple_response = '<?xml version="1.0" encoding="UTF-8" ?>
              <srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/" xmlns:xcql="http://www.loc.gov/zing/cql/xcql/">
              </srw:searchRetrieveResponse>';

    protected $simple_explain_response = '<?xml version="1.0" encoding="UTF-8"?>
            <sru:explainResponse xmlns:sru="http://www.loc.gov/zing/srw/">
            </sru:explainResponse>';

    public function testUrlTo()
    {
        $sru1 = new Client($this->url);
        $expectedUrl1 = $this->url . '?operation=searchRetrieve&version=1.1&recordSchema=marcxml&maximumRecords=10&query=isbn%3D123';
        $expectedUrl2 = $this->url . '?operation=searchRetrieve&version=1.1&recordSchema=marcxml&maximumRecords=50&query=isbn%3D123&startRecord=2';
        $expectedUrl5 = $this->url . '?operation=searchRetrieve&version=1.1&recordSchema=marcxml&maximumRecords=10&query=isbn%3D123&httpAccept=application%2Fxml';

        $sru3 = new Client($this->url, array('schema' => 'CUSTOMSCHEMA'));
        $expectedUrl3 = $this->url . '?operation=searchRetrieve&version=1.1&recordSchema=CUSTOMSCHEMA&maximumRecords=10&query=isbn%3D123';

        $sru4 = new Client($this->url, array('version' => '0.9'));
        $expectedUrl4 = $this->url . '?operation=searchRetrieve&version=0.9&recordSchema=marcxml&maximumRecords=10&query=isbn%3D123';

        $this->assertEquals($expectedUrl1, $sru1->urlTo('isbn=123'));
        $this->assertEquals($expectedUrl2, $sru1->urlTo('isbn=123', 2, 50));
        $this->assertEquals($expectedUrl3, $sru3->urlTo('isbn=123'));
        $this->assertEquals($expectedUrl4, $sru4->urlTo('isbn=123'));
        $this->assertEquals($expectedUrl5, $sru1->urlTo('isbn=123', 1, 10, array('httpAccept' => 'application/xml')));
    }
    
    public function testSearch()
    {
        $http = $this->httpMockSingleResponse($this->simple_response);
        $sru = new Client($this->url, null, $http);

        $this->assertXmlStringEqualsXmlString(
            $this->simple_response,
            $sru->search('test')->asXml()
        );
    }

    public function testSearchWithAuth()
    {
        $credentials = array('secretuser', 'secretpass');

        $request = m::mock();
        $request->shouldReceive('send')
            ->once()
            ->andReturn(new HttpResponse(200, null, $this->simple_response));

        $http = m::mock();
        $http->shouldReceive('get')
            ->with(m::any(), m::subset(array('auth' => $credentials)))
            ->once()
            ->andReturn($request);

        $options = array(
            'credentials' => $credentials
        );
        $sru = new Client($this->url, $options, $http);

        $r = $sru->search('test');
        $this->assertInstanceOf('Scriptotek\Sru\SearchRetrieveResponse', $r);
    }

    public function testNext()
    {
        $cql = 'dc.title="Joda jada isjda"';

        $request = m::mock();
        $request->shouldReceive('send')
            ->once()
            ->andReturn(new HttpResponse(200, null, '<?xml version="1.0" encoding="UTF-8" ?>
              <srw:searchRetrieveResponse 
                xmlns:srw="http://www.loc.gov/zing/srw/" 
                xmlns:xcql="http://www.loc.gov/zing/cql/xcql/"
              >
                <srw:version>1.1</srw:version>
                <srw:numberOfRecords>3</srw:numberOfRecords>
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
                  <srw:query>' . $cql . '</srw:query>
                  <srw:startRecord>1</srw:startRecord>
                  <srw:maximumRecords>2</srw:maximumRecords>
                  <srw:recordSchema>marcxchange</srw:recordSchema>
                </srw:echoedSearchRetrieveRequest>
                <srw:extraResponseData>
                  <responseDate>2014-03-28T12:09:50Z</responseDate>
                </srw:extraResponseData>
              </srw:searchRetrieveResponse>
            '));

        $http = m::mock();
        $http->shouldReceive('get')
            ->once()
            ->andReturn($request);

        $sru = new Client($this->url, null, $http);
        $response = $sru->search($cql);
        $this->assertCount(2, $response->records);

        $request->shouldReceive('send')
            ->once()
            ->andReturn(new HttpResponse(200, null, '<?xml version="1.0" encoding="UTF-8" ?>
              <srw:searchRetrieveResponse 
                xmlns:srw="http://www.loc.gov/zing/srw/" 
                xmlns:xcql="http://www.loc.gov/zing/cql/xcql/"
              >
                <srw:version>1.1</srw:version>
                <srw:numberOfRecords>3</srw:numberOfRecords>
                <srw:records>
                  <srw:record>
                    <srw:recordSchema>marcxchange</srw:recordSchema>
                    <srw:recordPacking>xml</srw:recordPacking>
                    <srw:recordPosition>3</srw:recordPosition>
                    <srw:recordData>Record 3</srw:recordData>
                  </srw:record>
                </srw:records>
                <srw:echoedSearchRetrieveRequest>
                  <srw:operation>searchRetrieve</srw:operation>
                  <srw:version>1.1</srw:version>
                  <srw:query>' . $cql . '</srw:query>
                  <srw:startRecord>3</srw:startRecord>
                  <srw:maximumRecords>2</srw:maximumRecords>
                  <srw:recordSchema>marcxchange</srw:recordSchema>
                </srw:echoedSearchRetrieveRequest>
                <srw:extraResponseData>
                  <responseDate>2014-03-28T12:09:50Z</responseDate>
                </srw:extraResponseData>
              </srw:searchRetrieveResponse>
            '));

        $response = $response->next();
        $this->assertCount(1, $response->records);

        $response = $response->next();
        $this->assertNull($response);


    }

    public function testHttpOptions()
    {
        $sru1 = new Client($this->url, array(
            'user-agent' => 'Blablabla/0.1',
            'credentials' => array('myuser', 'mypass'),
            'proxy' => 'proxyhost:80'
        ));

        $opts = $sru1->getHttpOptions();

        $this->assertEquals('application/xml', $opts['headers']['Accept']);
        $this->assertEquals('Blablabla/0.1', $opts['headers']['User-Agent']);
        $this->assertEquals(array('myuser', 'mypass'), $opts['auth']);
        $this->assertEquals('proxyhost:80', $opts['proxy']);
    }

    public function testRecords()
    {
        $http = $this->httpMockSingleResponse($this->makeDummyResponse(1));

        $sru1 = new Client($this->url);
        $r = $sru1->records('test', 1, array(), $http);

        $this->assertInstanceOf('Scriptotek\Sru\Records', $r);
    }

    public function testExplain()
    {
        $http = $this->httpMockSingleResponse($this->simple_explain_response);
        $sru = new Client($this->url, null, $http);
        $exp = $sru->explain();

        $this->assertInstanceOf('Scriptotek\Sru\ExplainResponse', $exp);

        $this->assertXmlStringEqualsXmlString(
            $this->simple_explain_response,
            $exp->asXml()
        );
    }

}

