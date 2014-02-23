<?php
$timestamp = time();

$randomNumber = rand(1,999999);
$I = new ApiGuy($scenario);
$I->wantTo('PUT update the total amount of BTP and receive an XML result');
$I->sendGET('put/british_transport_police:' . $randomNumber . '/xml');
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<region id="British Transport Police" total="' . $randomNumber . '" previous=');
