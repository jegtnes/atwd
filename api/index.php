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

function returnAllCrime($sourceData, $json = false) {
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

function returnCrimeByRegion($regionName, $sourceData, $json = false) {
	if ($regionName === "england_and_wales") {
		return returnAllCrime($sourceData, $json);
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

if (file_exists(DATA_SOURCE)) {
	$request = parseApiRequest($_SERVER['REQUEST_URI']);

	$crimes = createBaseCrimeXml($request['year']);
	$crime = returnCrimeByRegion($request['region'], DATA_SOURCE);
	$crime = $crimes->importNode($crime, true);
	$crimes->documentElement->firstChild->appendChild($crime);

	header('Content-Type: text/xml');
	echo $crimes->saveXML();
}
?>
