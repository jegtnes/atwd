<?php
$timestamp = time();
$I = new ApiGuy($scenario);
$I->wantTo('View an overview of all crime as XML');
$I->sendGET('xml');
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<response timestamp="' . $timestamp . '">');
$I->seeResponseContains('<region id="South West" total=');
$I->seeResponseContains('<region id="London" total=');
$I->seeResponseContains('<national id="British Transport Police" total=');
$I->seeResponseContains('<national id="Action Fraud" total=');
$I->seeResponseContains('<england total=');
$I->seeResponseContains('<wales total=');
