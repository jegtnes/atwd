<?php
$timestamp = time();
$I = new ApiGuy($scenario);
$I->wantTo('View an overview of all crime as JSON');
$I->sendGET('json');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('timestamp' => "$timestamp"));
$I->seeResponseContains('"region": [');
$I->seeResponseContains('"id": "South West",');
$I->seeResponseContains('"id": "London",');
$I->seeResponseContains('"national": [');
$I->seeResponseContains('"england": {');
$I->seeResponseContains('"wales": {');
