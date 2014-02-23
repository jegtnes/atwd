<?php
$timestamp = time();
$I = new ApiGuy($scenario);
$I->wantTo('View an overview of all crime as XML');
$I->sendGET('xml');
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains("<response timestamp=\"$timestamp\">");
