<?php
use \ApiGuy;

class PostWessexAsXmlCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function postWessexAsXml(ApiGuy $I) {
    	$violenceWithoutInjury = rand(1,100);
    	$violenceWithInjury = rand(1,$violenceWithoutInjury);
    	$homicide = rand(1,$violenceWithInjury);
    	$I->wantTo('Create a new area in the South West called Wessex and see a response as XML');
    	$I->sendGET('post/south_west/wessex/hom:4-vwi:15-vwoi:25/xml');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsXml();
    }
}
