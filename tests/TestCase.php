<?php namespace Scriptotek\Tests;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase {


    protected function basicHttpMock($response)
    {
        $request = m::mock();
        $request->shouldReceive('send')
            ->once()
            ->andReturn(new HttpResponse(200, null, $response));

        $http = m::mock();
        $http->shouldReceive('get')
            ->once()
            ->andReturn($request);

        return $http;
    }

}

