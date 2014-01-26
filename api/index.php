<?php
header('Content-Type: text/xml');
require ("../library/utilities.php");

function createBaseCrimeXml($sourceData, $year) {
	$xml = new SimpleXMLElement("<response></response>");
	$xml->addAttribute('timestamp', time());
	$response = $xml->addChild('crimes');
	$response->addAttribute('year', $year);
	return $response;
}

if (file_exists(DATA_SOURCE)) {

	$sourceData = simplexml_load_file(DATA_SOURCE);

	$request = parseApiRequest($_SERVER['REQUEST_URI']);

	$crimes = createBaseCrimeXml(DATA_SOURCE, $request['year']);

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
	echo $crimes->asXML();
}
?>
