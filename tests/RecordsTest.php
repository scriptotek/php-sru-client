<?php namespace Scriptotek\Sru;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;

class RecordsTest extends TestCase {

	public function testIterating()
	{
		$cql = 'dummy';
		$uri = 'http://localhost';
		$n = 8;
		$response = $this->makeDummyResponse($n);
        $http = $this->httpMockSingleResponse($response);

		$client = new Client($uri);
		$records = new Records($cql, $client, 10, $http);
		$this->assertNull($records->error);
		$this->assertEquals(8, $records->numberOfRecords());
		$records->rewind();
		$this->assertNull($records->error);

		$this->assertEquals(1, $records->key());
		$this->assertTrue($records->valid());
		$records->next();
		$records->next();
		$this->assertEquals(3, $records->key());
		$this->assertTrue($records->valid());
		$records->rewind();
		$this->assertEquals(1, $records->key());
		$this->assertTrue($records->valid());

		$i = 0;
		foreach ($records as $rec) {
			$i++;
		}
		$this->assertEquals($n, $i);
	}

	/**
	 * @expectedException Scriptotek\Sru\InvalidResponseException
	 */
	public function testRepeatSameResponse()
	{
		// Result set contains two records
		$response = $this->makeDummyResponse(2, array('maxRecords' => 1));

        $http = $this->httpMockSingleResponse($response);
        $uri = 'http://localhost';
        $cql = 'dummy';

		// Request only one record in each request
		$client = new Client($uri);
		$rec = new Records($cql, $client, 1, $http);

		// Jumping to position 2 should call fetchMore() and throw 
		// an InvalidResponseException on getting the same response
		// as we got for position 1
		$rec->next(); 
	}
	

	public function testMultipleRequests()
	{
		$nrecs = 5;

		$responses = array(
			$this->makeDummyResponse($nrecs, array('startRecord' => 1, 'maxRecords' => 2)),
			$this->makeDummyResponse($nrecs, array('startRecord' => 3, 'maxRecords' => 2)),
			$this->makeDummyResponse($nrecs, array('startRecord' => 5, 'maxRecords' => 2))
		);

        $http = $this->httpMockListResponse($responses);
        $uri = 'http://localhost';
        $cql = 'dummy';

		$client = new Client($uri);
		$records = new Records($cql, $client, 10, $http);

		$records->rewind();
		foreach (range(1, $nrecs) as $n) {
			$this->assertEquals($n, $records->key());
			$this->assertTrue($records->valid());
			$this->assertEquals($n, $records->current()->position);
			$records->next();
		}
		$this->assertFalse($records->valid());
	}

}