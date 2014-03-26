<?php namespace Scriptotek;

use \Guzzle\Http\Message\Response;
use \Mockery as m;

class SruTestCase extends \PHPUnit_Framework_TestCase {


    protected function basicHttpMock($response)
    {
        $request = m::mock();
        $request->shouldReceive('send')
            ->once()
            ->andReturn(new Response(200, null, $response));

        $http = m::mock();
        $http->shouldReceive('get')
            ->once()
            ->andReturn($request);

        return $http;
    }

}

