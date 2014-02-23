<?php
namespace Codeception\Module;

// here you can define custom functions for ApiGuy

class ApiHelper extends \Codeception\Module
{
	public function printAVariable($var){
        $this->debug($var);
    }
}
