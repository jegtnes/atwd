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
		$I->seeResponseContains('<response timestamp="' . $timestamp . '">');
		$I->seeResponseContains('<region id="British Transport Police" total="' . $this->randomNumber . '" previous=');
		$response = (string) $I->grabResponse();
		preg_match("/total=\"([0-9]*)\" previous=\"([0-9]*)\"/", $response, $amounts);
		if ($amounts) $this->previous = $amounts[2];
	}

	public function testResetValue(ApiGuy $I) {
		$timestamp = time();
		$I->wantTo('PUT update the total amount of BTP back to the original value and receive an XML result');
		$I->sendGET('put/british_transport_police:' . $this->previous . '/xml');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsXml();
		$I->seeResponseContains('<response timestamp="' . $timestamp . '">');
		$I->seeResponseContains('<region id="British Transport Police" total="' . $this->previous . '" previous="' . $this->randomNumber . '"/>');
		unset($this->randomNumber);
		unset($this->previous);
	}
}
