<?php
use \ApiGuy;

class DeleteWessexAsXmlCest
{

	public $timestamp;

	public function _before()
	{
		$this->timestamp = time();
	}

	public function _after()
	{
		unset($this->timestamp);
	}

	public function deleteWessexAsXml(ApiGuy $I) {
		$I->wantTo('Delete the Wessex area and see a response as XML');
		$I->sendGET("delete/wessex/xml");
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsXml();
		$I->seeResponseContains('<response timestamp="' . $this->timestamp . '">');
		$I->seeResponseContains('<region id="South West" total=');
		$I->seeResponseContains('<area id="Wessex" deleted="');
		$I->seeResponseContains('<deleted id="Homicide" total="');
		$I->seeResponseContains('<deleted id="Violence with injury" total="');
		$I->seeResponseContains('<deleted id="Violence without injury" total="');
		$I->seeResponseContains('<england total=');
		$I->seeResponseContains('<england_wales total=');
	}

}
