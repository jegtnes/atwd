<?php

function getAll($sourceData) {
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
