<?php

class Request {
	private $crimeXml;
	private $data;

	function __construct($sourceData) {
		$crimeXml = new DOMDocument;
		$data = $crimeXml->createDocumentFragment();
	}

	public function getBaseElement() {
		return $this->data;
	}

	public function getSourceData() {
		return $this->crimeXml;
	}
}
