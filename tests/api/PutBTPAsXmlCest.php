<?php
use \ApiGuy;

class PutBTPAsXmlCest
{

	public $randomNumber;
	public $previous;

	public function testOriginal(ApiGuy $I) {
		$timestamp = time();
		$this->randomNumber = rand(1,999999);
		$I->wantTo('PUT update the total amount of BTP and receive an XML result');
		$I->sendGET('put/british_transport_police:' . $this->randomNumber . '/xml');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsXml();
		$I->seeResponseContains('<region id="British Transport Police" total="' . $this->randomNumber . '" previous=');
		$response = (string) $I->grabResponse();
		preg_match("/total=\"([0-9]*)\" previous=\"([0-9]*)\"/", $response, $amounts);
		$this->previous = $amounts[2];
    }

}
