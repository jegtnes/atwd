<?php
header('Content-Type: text/xml');
require ("../library/utilities.php");

function createBaseCrimeXml($year) {
	$xml = new DOMDocument('1.0', 'utf-8');
	$root = $xml->createElement('response');
	$root->setAttribute('timestamp', time());

	$response = $xml->createElement('crimes');
	$response = $xml->appendChild($response);
	$response->setAttribute('year', $year);
	return $xml;
}

if (file_exists(DATA_SOURCE)) {

	$sourceData = simplexml_load_file(DATA_SOURCE);

	$request = parseApiRequest($_SERVER['REQUEST_URI']);

	$crimes = createBaseCrimeXml($request['year']);
	echo $crimes->saveXML();

	// echo $crimes->saveXML();
}
?>
