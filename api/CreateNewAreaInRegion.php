<?php
function createNewAreaInRegion($areaName, $regionName, $violenceWithoutInjury, $violenceWithInjury, $homicide, $sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$xPath = new DOMXPath($crimeXml);

	$regionName = ucwords(str_replace('_', ' ', $regionName));
	$region = $xPath->query("//region[@id='$regionName']")->item(0);
	$regionId = $region->attributes->getNamedItem("id")->nodeValue;

	$regionElement = $data->appendChild($crimeXml->createElement('region'));
	$regionElement->setAttribute('id', $regionId);

	$areaName = ucwords(str_replace('_', ' ', $areaName));
	$areaTotal = $violenceWithoutInjury + $violenceWithInjury + $homicide;
	$areaElement = $regionElement->appendChild($crimeXml->createElement('area'));
	$areaElement->setAttribute('id', $areaName);
	$areaElement->setAttribute('total', $areaTotal);

	$homicideElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$homicideElement->setAttribute('id', "Homicide");
	$homicideElement->setAttribute('total', $homicide);

	$violenceWithInjuryElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$violenceWithInjuryElement->setAttribute('id', "Violence with injury");
	$violenceWithInjuryElement->setAttribute('total', $violenceWithInjury);

	$violenceWithoutInjuryElement = $areaElement->appendChild($crimeXml->createElement('recorded'));
	$violenceWithoutInjuryElement->setAttribute('id', "Violence without injury");
	$violenceWithoutInjuryElement->setAttribute('total', $violenceWithoutInjury);

	// To update this in the XML, remove any existing areas with this name
	if ($xPath->query("//area[@id='$areaName']")->item(0)) {
		$area = $xPath->query("//area[@id='$areaName']")->item(0);
		$area->parentNode->removeChild($area);
	}

	//Used to calculate the new totals for region & country
	$previousAreaTotal = isset($area) ? $area->attributes->getNamedItem("total")->nodeValue : 0;

	$england = $xPath->query("//country[@id='England']")->item(0);
	$englandTotal = $england->attributes->getNamedItem("total")->nodeValue;
	$englandElement = $data->appendChild($crimeXml->createElement('england'));
	$englandElement->setAttribute('total', $englandTotal + $areaTotal - $previousAreaTotal);
	$walesTotal = $xPath->query("//country[@id='Wales']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$actionfraudTotal = $xPath->query("//national[@id='Action Fraud']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$btpTotal = $xPath->query("//national[@id='British Transport Police']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$englandAndWalesElement = $data->appendChild($crimeXml->createElement('england_wales'));
	$englandAndWalesElement->setAttribute('total', $englandTotal + $walesTotal + $actionfraudTotal + $btpTotal + $areaTotal - $previousAreaTotal);

	$regionTotal = $region->attributes->getNamedItem("total")->nodeValue - $previousAreaTotal + $areaTotal;
	$regionElement->setAttribute('total', $regionTotal);
	$england->setAttribute('total', $englandTotal - $previousAreaTotal + $areaTotal);
	$region->attributes->getNamedItem("total")->nodeValue = $regionTotal;

	// Needed to keep the original Area fragment in the output result
	$areaClone = $areaElement->cloneNode(true);

	//and append to the region, and save
	$region->appendChild($areaClone);
	$crimeXml->save($sourceData);
	return $data;
}
