<?php
header('Content-Type: text/xml');
require ("../library/utilities.php");

if (file_exists(DATA_SOURCE)) {
	$sourceData = simplexml_load_file(DATA_SOURCE);

	$request = parseApiRequest($_SERVER['REQUEST_URI']);

	$responseXml = new SimpleXMLElement("<response></response>");
	$responseXml->addAttribute('timestamp', time());
	$crimes = $responseXml->addChild('crimes');
	$crimes->addAttribute('year', $request['year']);

	foreach ($sourceData->region as $x) {
		$element = $crimes->addChild('region');
		$element->addAttribute('id', $x['id']);
		$element->addAttribute('total', $x['total']);
	}

	foreach ($sourceData->national as $x) {
		$element = $crimes->addChild('national');
		$element->addAttribute('id', $x['id']);
		$element->addAttribute('total', $x['total']);
	}

	foreach ($sourceData->country as $x) {
		$element = $crimes->addChild(lcfirst($x['id']));
		$element->addAttribute('total', $x['total']);
	}
	echo $responseXml->asXML();
}
?>
