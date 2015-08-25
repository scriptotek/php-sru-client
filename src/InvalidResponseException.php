<?php namespace Scriptotek\Sru;

class InvalidResponseException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
