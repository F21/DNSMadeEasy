<?php
namespace DNSMadeEasy\exception;

class ResultErrorException extends \Exception{

	private $result;

	public function __construct(\DNSMadeEasy\Result $result){
		$this->result = $result;

		parent::__construct($result->__toString(), $result->statusCode);
	}

	public function getResult(){
		return $this->result;
	}
}