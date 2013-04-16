<?php

class EHiveCaptchaException extends Exception {
	protected $message;
	
	public function __construct($message = null) {
		$this->message = $message;	
	}
}