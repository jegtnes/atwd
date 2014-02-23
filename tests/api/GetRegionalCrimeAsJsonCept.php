<?php
$timestamp = time();
$I = new ApiGuy($scenario);
$I->wantTo('View all regional crime in the south west with respective areas as JSON');
$I->sendGET('south_west/json');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('timestamp' => "$timestamp"));
$I->seeResponseContainsJson(array("id" => "South West"));
$I->seeResponseContains('"area": [');
$I->seeResponseContains('"id": "Avon and Somerset",');
$I->seeResponseContains('"id": "Devon and Cornwall",');
$I->seeResponseContains('"id": "Dorset",');
$I->seeResponseContains('"id": "Gloucestershire",');
$I->seeResponseContains('"id": "Wiltshire",');
