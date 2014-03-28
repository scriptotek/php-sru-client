<?php namespace Scriptotek\Tests;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;
use \Scriptotek\Sru\Client as SruClient;

class ClientTest extends TestCase {

    protected $url = 'http://sru.my_fictive_host.net';

    protected $simple_response = '<?xml version="1.0" encoding="UTF-8" ?>
              <srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/" xmlns:xcql="http://www.loc.gov/zing/cql/xcql/">
              </srw:searchRetrieveResponse>';

    public function testUrlTo()
    {
        $sru1 = new SruClient($this->url);
        $expectedUrl1 = $this->url . '?version=1.1&operation=searchRetrieve&recordSchema=marcxml&maximumRecords=10&query=isbn%3D123';
        $expectedUrl2 = $this->url . '?version=1.1&operation=searchRetrieve&recordSchema=marcxml&maximumRecords=50&query=isbn%3D123&startRecord=2';

        $sru3 = new SruClient($this->url, array('schema' => 'CUSTOMSCHEMA'));
        $expectedUrl3 = $this->url . '?version=1.1&operation=searchRetrieve&recordSchema=CUSTOMSCHEMA&maximumRecords=10&query=isbn%3D123';

        $sru4 = new SruClient($this->url, array('version' => '0.9'));
        $expectedUrl4 = $this->url . '?version=0.9&operation=searchRetrieve&recordSchema=marcxml&maximumRecords=10&query=isbn%3D123';

        $this->assertEquals($expectedUrl1, $sru1->urlTo('isbn=123'));
        $this->assertEquals($expectedUrl2, $sru1->urlTo('isbn=123', 2, 50));
        $this->assertEquals($expectedUrl3, $sru3->urlTo('isbn=123'));
        $this->assertEquals($expectedUrl4, $sru4->urlTo('isbn=123'));
    }
    
    public function testSearch()
    {
        $http = $this->basicHttpMock($this->simple_response); 
        $sru = new SruClient($this->url, null, $http);

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
        $sru = new SruClient($this->url, $options, $http);

        $sru->search('test');
    }

}

