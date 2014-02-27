<?php

require ("../library/utilities.php");
require ("Request.php");
require ("GetAll.php");
require ("GetAreaByRegion.php");
require ("CreateNewAreaInRegion.php");
require ("UpdateCrimeByRegion.php");
require ("DeleteArea.php");

function createBaseCrimeXml($year) {
	$xml = new DOMDocument('1.0', 'utf-8');
	$root = $xml->createElement('response');
	$root->setAttribute('timestamp', time());
	$root = $xml->appendChild($root);

	$response = $xml->createElement('crimes');
	$response->setAttribute('year', $year);
	$response = $root->appendChild($response);
	return $xml;
}

if (file_exists(DATA_SOURCE)) {

	$request = parseApiRequest($_SERVER['REQUEST_URI']);
	$crimes = createBaseCrimeXml($request['year']);

	switch ($request['verb']) {
		case 'put':
			$crime = updateCrimeByRegion($request['region'], $request['update_amount'], DATA_SOURCE);
			break;

		case 'post':
			$crime = createNewAreaInRegion($request['area'], $request['region'], $request['crime_values']['vwoi'], $request['crime_values']['vwi'], $request['crime_values']['hom'], DATA_SOURCE);
			break;

		case 'delete':
			$crime = deleteArea($request['area'], DATA_SOURCE);
			break;

		case 'get':
			if ($request['region'] === 'england_and_wales') {
				$crime = getAll(DATA_SOURCE);
			}
			else {
				$crime = returnCrimeByRegion($request['region'], DATA_SOURCE);
			}
			break;

		default:
			generateXmlError(501, "Service error.");
	}

	$crime = $crimes->importNode($crime, true);
	$crimes->documentElement->firstChild->appendChild($crime);

	if ($request['response_format'] === 'json') {
		header('Content-Type: application/json');
		$xml = simplexml_load_string($crimes->saveXML());
		$json = json_encode($xml, JSON_PRETTY_PRINT);
		$json_array = object_to_array(json_decode($json));

		// going one level deeper: wrap the entire thing in a response array because the spec said so
		$assignment_compliant_json_array['response'] = extractJsonAttributes($json_array);
		echo json_encode($assignment_compliant_json_array, JSON_PRETTY_PRINT);
	}

	elseif ($request['response_format'] === 'xml') {
		header('Content-Type: text/xml');
		echo $crimes->saveXML();
	}
}
?>
