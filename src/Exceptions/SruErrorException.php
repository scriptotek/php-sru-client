<?php namespace Scriptotek\Sru\Exceptions;

class SruErrorException extends \Exception
{
	public $uri;

    /**
     * @param string $message
     */
    public function __construct($message, $uri)
    {
        parent::__construct($message);
        $this->uri = $uri;
    }
}
