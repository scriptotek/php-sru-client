<?php namespace Scriptotek\Sru;

class InvalidResponseException extends \Exception{

	public function __construct($message) {
		parent::__construct($message);
	}

}
