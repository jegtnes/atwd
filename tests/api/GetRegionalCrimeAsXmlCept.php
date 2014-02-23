<?php
$timestamp = time();
$I = new ApiGuy($scenario);
$I->wantTo('View all regional crime in the south west with respective areas as XML');
$I->sendGET('south_west/xml');
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<response timestamp="' . $timestamp . '">');
$I->seeResponseContains('<region id="South West" total="');
$I->seeResponseContains('<area id="Avon and Somerset" total="');
$I->seeResponseContains('<area id="Devon and Cornwall" total="');
$I->seeResponseContains('<area id="Dorset" total="');
$I->seeResponseContains('<area id="Gloucestershire" total="');
$I->seeResponseContains('<area id="Wiltshire" total="');
