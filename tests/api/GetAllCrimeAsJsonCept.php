<?php
$I = new ApiGuy($scenario);
$I->wantTo('View an overview of all crime as JSON');
$I->sendGET('json');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
