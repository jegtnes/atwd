<?php
$I = new ApiGuy($scenario);
$I->wantTo('View an overview of all crime as XML');
$I->seeResponseCodeIs(200);
