<?php
use \ApiGuy;

class PutBTPAsJsonCest
{

	public $randomNumber;
	public $previous;
	public $timestamp;

	public function _before()
	{
		$this->timestamp = time();
	}

	public function _after()
	{
		unset($this->timestamp);
	}

	public function testOriginal(ApiGuy $I) {
		$timestamp = time();
		$this->randomNumber = rand(1,999999);
		$I->wantTo('PUT update the total amount of BTP and receive a JSON result');
		$I->sendGET('put/british_transport_police:' . $this->randomNumber . '/json');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(array('timestamp' => "$timestamp"));
		$I->seeResponseContains('"region": {');
		$I->seeResponseContains('"id": "British Transport Police",');
		$I->seeResponseContains('"total": "' . $this->randomNumber . '",');
		$response = (string)($I->grabResponse());

		preg_match("/\"previous\": ?\"([0-9]*)\"/", $response, $amounts);
		$I->printAVariable($amounts);
		if ($amounts) $this->previous = $amounts[1];
	}

	public function testResetValue(ApiGuy $I) {
		$timestamp = time();
		$I->wantTo('PUT update the total amount of BTP back to the original value and receive a JSON result');
		$I->sendGET('put/british_transport_police:' . $this->previous . '/json');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(array('timestamp' => "$timestamp"));
		$I->seeResponseContains('"region": {');
		$I->seeResponseContains('"id": "British Transport Police",');
		$I->seeResponseContains('"total": "' . $this->previous . '",');
		$I->seeResponseContains('"previous": "' . $this->randomNumber . '"');
		unset($this->randomNumber);
		unset($this->previous);
	}

}
