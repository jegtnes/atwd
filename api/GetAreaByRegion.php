<?php
function returnCrimeByRegion($regionName, $sourceData) {
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
