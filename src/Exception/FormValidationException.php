<?php 	

namespace Mchuluq\Laravel\Uac\Exception;	

use \Exception;	

class FormValidationException extends Exception{	

    protected $details = [];	

    public function __construct($message, $details=[]) {	
        parent::__construct($message, 0);	
        $this->details = $details;	
    }	

    public function __toString() {	
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";	
    }	

    public function getDetails() {	
        return $this->details;	
    }	

    public function messages(){	
        return $this->details->all();	
    }	

    public function message($key){	
        $items = $this->details->all();	
        return (array_key_exists($key,$items) ? $items[$key] : null);	
    }	
} 