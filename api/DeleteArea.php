<?php
function deleteArea($areaName, $sourceData) {
	$crimeXml = new DOMDocument;
	$crimeXml->load($sourceData);
	$data = $crimeXml->createDocumentFragment();
	$areaName = ucwords(str_replace('_', ' ', $areaName));
	$xPath = new DOMXPath($crimeXml);
	$area = $xPath->query("//area[@id='$areaName']")->item(0);

	if (!$area) {
		generateXmlError(404, "Area not found.");
	}

	$areaTotal = $area->attributes->getNamedItem("total")->nodeValue;
	$homicide = $xPath->query("//area[@id='$areaName']/recorded[@id='Homicide']")->item(0);
	$homicideTotal = $homicide->attributes->getNamedItem("total")->nodeValue;
	$violenceWithInjury = $xPath->query("//area[@id='$areaName']/recorded[@id='Violence with injury']")->item(0);
	$violenceWithInjuryTotal = $violenceWithInjury->attributes->getNamedItem("total")->nodeValue;
	$violenceWithoutInjury = $xPath->query("//area[@id='$areaName']/recorded[@id='Violence without injury']")->item(0);
	$violenceWithoutInjuryTotal = $violenceWithoutInjury->attributes->getNamedItem("total")->nodeValue;

	$parent = $area->parentNode;
	$parentTotal = $parent->attributes->getNamedItem("total")->nodeValue;
	$parent->setAttribute('total', $parentTotal - $areaTotal);

	$area->parentNode->removeChild($area);

	$areaElement = $data->appendChild($crimeXml->createElement('area'));
	$areaElement->setAttribute('id', $areaName);
	$areaElement->setAttribute('deleted', $areaTotal);

	$homicideElement = $areaElement->appendChild($crimeXml->createElement('deleted'));
	$homicideElement->setAttribute('id', "Homicide");
	$homicideElement->setAttribute('total', $homicideTotal);

	$violenceWithInjuryElement = $areaElement->appendChild($crimeXml->createElement('deleted'));
	$violenceWithInjuryElement->setAttribute('id', "Violence with injury");
	$violenceWithInjuryElement->setAttribute('total', $violenceWithInjuryTotal);

	$violenceWithoutInjuryElement = $areaElement->appendChild($crimeXml->createElement('deleted'));
	$violenceWithoutInjuryElement->setAttribute('id', "Violence without injury");
	$violenceWithoutInjuryElement->setAttribute('total', $violenceWithoutInjuryTotal);

	$england = $xPath->query("//country[@id='England']")->item(0);
	$englandTotal = $england->attributes->getNamedItem("total")->nodeValue;
	$englandElement = $data->appendChild($crimeXml->createElement('england'));
	$england->setAttribute('total', (int)$englandTotal - (int)$areaTotal);
	$englandElement->setAttribute('total', (int)$englandTotal - (int)$areaTotal);
	$walesTotal = $xPath->query("//country[@id='Wales']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$actionfraudTotal = $xPath->query("//national[@id='Action Fraud']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$btpTotal = $xPath->query("//national[@id='British Transport Police']")->item(0)->attributes->getNamedItem("total")->nodeValue;
	$englandAndWalesElement = $data->appendChild($crimeXml->createElement('england_wales'));
	$englandAndWalesElement->setAttribute('total', ($englandTotal + $walesTotal + $actionfraudTotal + $btpTotal) - $areaTotal);

	$crimeXml->save($sourceData);

	return $data;
}
