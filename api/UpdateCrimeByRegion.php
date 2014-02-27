<?php
function updateCrimeByRegion($regionName, $updateAmount, $sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$xPath = new DOMXPath($crimeXml);
	$regionName = ucwords(str_replace('_', ' ', $regionName));

	$region = $xPath->query("//national[@id='$regionName']")->item(0);

	if (!$region) {
		generateXmlError(404, "Region not found.");
	}

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
