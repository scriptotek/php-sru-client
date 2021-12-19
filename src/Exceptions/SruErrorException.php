<?php

namespace Scriptotek\Sru\Exceptions;

class SruErrorException extends \Exception
{
    public $uri;
    public $requestUrl;

    /**
     * @param string $message
     * @param string $uri
     * @param string $requestUrl
     */
    public function __construct($message, $uri, $requestUrl)
    {
        parent::__construct("Request to $requestUrl failed with error: $message");
        $this->uri = $uri;
        $this->requestUrl = $requestUrl;
    }
}
