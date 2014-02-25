<?php

require ("../library/utilities.php");

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

function returnAllCrime($sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$regions = $crimeXml->getElementsByTagName('region');
	$nationals = $crimeXml->getElementsByTagName('national');
	$countries = $crimeXml->getElementsByTagName('country');

	foreach ($regions as $region) {
		$element = $crimeXml->createElement('region');
		$element->setAttribute("id", $region->attributes->getNamedItem("id")->nodeValue);
		$element->setAttribute("total", $region->attributes->getNamedItem("total")->nodeValue);
		$data->appendChild($element);
	}

	foreach ($nationals as $national) {
		$element = $crimeXml->createElement('national');
		$element->setAttribute("id", $national->attributes->getNamedItem("id")->nodeValue);
		$element->setAttribute("total", $national->attributes->getNamedItem("total")->nodeValue);
		$data->appendChild($element);
	}

	foreach ($countries as $country) {
		$element = $crimeXml->createElement(lcfirst($country->attributes->getNamedItem("id")->nodeValue));
		$element->setAttribute("total", $country->attributes->getNamedItem("total")->nodeValue);
		$data->appendChild($element);
	}
	return $data;
}

function returnCrimeByRegion($regionName, $sourceData) {
	if ($regionName === "england_and_wales") {
		return returnAllCrime($sourceData);
	}
	else {
		$crimeXml = new DOMDocument;
		$crimeXml->load($sourceData);
		$data = $crimeXml->createDocumentFragment();
		$xPath = new DOMXPath($crimeXml);

		// turns underscored param to what's stored in the ID and we need to find
		// i.e. south_west to South West
		$regionName = ucwords(str_replace('_', ' ', $regionName));

		// as we're looking for nationals as well as regions, local-name() is needed here.
		// kudos to Jens Erat @ StackOverflow http://stackoverflow.com/a/20122323/1430657
		$region = $xPath->query("/crimes/*[local-name() = 'region' or local-name() = 'national'][@id='$regionName']")->item(0);

		// nationals don't have regions, so this fails gracefully by
		// not selecting anything if on a national
		$areas = $xPath->query("//region[@id='$regionName']/area");

		$regionId = $region->attributes->getNamedItem("id")->nodeValue;
		$regionTotal = $region->attributes->getNamedItem("total")->nodeValue;

		$regionElement = $crimeXml->createElement('region');
		$regionElement->setAttribute('id', $regionId);
		$regionElement->setAttribute('total', $regionTotal);
		$regionElement = $data->appendChild($regionElement);

		foreach ($areas as $area) {
			$areaId = $area->attributes->getNamedItem("id")->nodeValue;
			$areaTotal = $area->attributes->getNamedItem("total")->nodeValue;
			$areaElement = $crimeXml->createElement('area');
			$areaElement->setAttribute('id', $areaId);
			$areaElement->setAttribute('total', $areaTotal);
			$areaElement = $data->appendChild($areaElement);

			while ($area->hasChildNodes()){
				$area->removeChild($area->childNodes->item(0));
				$regionElement->appendChild($areaElement);
			}
		}
		return $data;
	}
}

function updateCrimeByRegion($regionName, $updateAmount, $sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$xPath = new DOMXPath($crimeXml);
	$regionName = ucwords(str_replace('_', ' ', $regionName));

	$region = $xPath->query("//national[@id='$regionName']")->item(0);

	$regionId = $region->attributes->getNamedItem("id")->nodeValue;
	$originalTotal = $region->attributes->getNamedItem("total")->nodeValue;
	$newTotal = $updateAmount;

	$regionElement = $crimeXml->createElement('region');
	$regionElement->setAttribute('id', $regionId);
	$regionElement->setAttribute('total', $newTotal);
	$regionElement->setAttribute('previous', $originalTotal);
	$regionElement = $data->appendChild($regionElement);

	$data->appendChild($regionElement);

	$region->setAttribute('total', $newTotal);
	$crimeXml->save($sourceData);

	return $data;
}

function createNewAreaInRegion($areaName, $regionName, $violenceWithoutInjury, $violenceWithInjury, $homicide, $sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$xPath = new DOMXPath($crimeXml);

	$regionName = ucwords(str_replace('_', ' ', $regionName));
	$region = $xPath->query("//region[@id='$regionName']")->item(0);
	$regionId = $region->attributes->getNamedItem("id")->nodeValue;
	$regionTotal = $region->attributes->getNamedItem("total")->nodeValue;

	$regionElement = $data->appendChild($crimeXml->createElement('region'));
	$regionElement->setAttribute('id', $regionId);
	$regionElement->setAttribute('total', $regionTotal);

	$areaName = ucwords(str_replace('_', ' ', $areaName));
	$areaTotal = $violenceWithoutInjury + $violenceWithInjury + $homicide;
	$areaElement = $regionElement->appendChild($crimeXml->createElement('area'));
	$areaElement->setAttribute('id', $areaName);
	$areaElement->setAttribute('total', $areaTotal);

	// Needed to keep the original Area fragment in the outputted result
	$areaClone = $areaElement->cloneNode(true);

	$homicideElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$homicideElement->setAttribute('id', "Homicide");
	$homicideElement->setAttribute('total', $homicide);

	$violenceWithInjuryElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$violenceWithInjuryElement->setAttribute('id', "Violence with injury");
	$violenceWithInjuryElement->setAttribute('total', $violenceWithInjury);

	$violenceWithoutInjuryElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$violenceWithoutInjuryElement->setAttribute('id', "Violence without injury");
	$violenceWithoutInjuryElement->setAttribute('total', $violenceWithoutInjury);

	$englandTotal = $xPath->query("//country[@id='England']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$englandElement = $data->appendChild($crimeXml->createElement('england'));
	$englandElement->setAttribute('total', $englandTotal + $areaTotal);
	$walesTotal = $xPath->query("//country[@id='Wales']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$actionfraudTotal = $xPath->query("//national[@id='Action Fraud']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$btpTotal = $xPath->query("//national[@id='British Transport Police']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$englandAndWalesElement = $data->appendChild($crimeXml->createElement('england_wales'));
	$englandAndWalesElement->setAttribute('total', $englandTotal + $walesTotal + $actionfraudTotal + $btpTotal + $areaTotal);

	$region->appendChild($areaClone);
	$crimeXml->save($sourceData);
	return $data;
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
			# code...
			break;

		case 'get':
			$crime = returnCrimeByRegion($request['region'], DATA_SOURCE);
			break;

		default:
			break;
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
