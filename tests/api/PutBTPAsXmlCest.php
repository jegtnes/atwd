<?php
use \ApiGuy;

class PutBTPAsXmlCest
{

	public $randomNumber;
	public $previous;

    public function _before()
    {
    	$this->randomNumber = rand(1,999999);
    }

    public function _after()
    {
    }

    // tests
    public function tryToTest(ApiGuy $I) {
    	$timestamp = time();
		$I->wantTo('PUT update the total amount of BTP and receive an XML result');
		$I->sendGET('put/british_transport_police:' . $this->randomNumber . '/xml');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsXml();
		$I->seeResponseContains('<region id="British Transport Police" total="' . $this->randomNumber . '" previous=');
    }

}
