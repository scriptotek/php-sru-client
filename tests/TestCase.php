<?php namespace Scriptotek\Sru;

use \Guzzle\Http\Message\Response as HttpResponse;
use \Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase {

    protected $recordTpl = '<srw:record>
            <srw:recordSchema>marcxchange</srw:recordSchema>
            <srw:recordPacking>xml</srw:recordPacking>
            <srw:recordPosition>{{position}}</srw:recordPosition>
            <srw:recordData>{{data}}</srw:recordData>
          </srw:record>';

    protected $mainTpl = '<?xml version="1.0" encoding="UTF-8" ?>
      <srw:searchRetrieveResponse 
        xmlns:srw="http://www.loc.gov/zing/srw/" 
        xmlns:xcql="http://www.loc.gov/zing/cql/xcql/"
      >
        <srw:version>1.1</srw:version>
        <srw:numberOfRecords>{{numberOfRecords}}</srw:numberOfRecords>
        <srw:records>
          {{records}}
        </srw:records>
        <srw:echoedSearchRetrieveRequest>
          <srw:operation>searchRetrieve</srw:operation>
          <srw:version>1.1</srw:version>
          <srw:query>{{cql}}</srw:query>
          <srw:startRecord>{{startRecord}}</srw:startRecord>
          <srw:maximumRecords>{{maxRecords}}</srw:maximumRecords>
          <srw:recordSchema>marcxchange</srw:recordSchema>
        </srw:echoedSearchRetrieveRequest>
        <srw:extraResponseData>
          <responseDate>2014-03-28T12:09:50Z</responseDate>
        </srw:extraResponseData>
      </srw:searchRetrieveResponse>';

    /**
     * Get an item from an array using "dot" notation.
     * Source: http://laravel.com/api/source-function-array_get.html#226-251
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    private function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) or ! array_key_exists($segment, $array))
            {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     *  numberOfRecords : Total number of records in response
     */
    public function makeDummyResponse($numberOfRecords = 10, $options = array())
    {
        // Request: CQL
        $cql = $this->array_get( $options, 'cql', 'dummy' );

        // Request: First record to fetch
        $startRecord = $this->array_get( $options, 'startRecord', 1 );

        // Request: Max number of records to return
        $maxRecords = $this->array_get( $options, 'maxRecords', 10 );

        $endRecord = $startRecord + min($maxRecords - 1, $numberOfRecords - $startRecord);

        $recordTpl = $this->recordTpl;
        $records = implode('', array_map(function($n) use ($recordTpl) {
            return str_replace(
                array('{{position}}', '{{data}}'),
                array($n, 'RecordData #' . $n),
                $recordTpl
            );
        }, range($startRecord, $endRecord)));

        return str_replace(
            array('{{records}}', '{{cql}}', '{{startRecord}}', '{{maxRecords}}', '{{numberOfRecords}}'),
            array($records, $cql, $startRecord, $maxRecords, $numberOfRecords),
            $this->mainTpl
        );
    }

    /**
     * Return a single response (no matter what request)
     */
    protected function httpMockSingleResponse($response)
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

    /**
     * Returns a series of responses (no matter what request)
     */
    protected function httpMockListResponse($responses)
    {
        $request = m::mock();
        $request->shouldReceive('send')
            ->andReturnValues(array_map(function($r) {
                return new HttpResponse(200, null, $r);
            }, $responses));

        $http = m::mock();
        $http->shouldReceive('get')
            ->andReturn($request);

        return $http;
    }

}

