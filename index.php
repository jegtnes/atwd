<?php
// needed to work around issues with OS X/Excel line endings
ini_set('auto_detect_line_endings', true);

function twoColCsvToXml($filename) {
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$rowCount = 0;

	$data = array();

	$xml = new DOMDocument;

	$root = $xml->createElement('crimes');
	$root = $xml->appendChild($root);
	$fileHandle = fopen($filename, 'r');

	if ($fileHandle) {
		while ($row = fgetcsv($fileHandle, 0, ',')) {
			$rowCount++;

			// dealing with two sets of headers here
			// add area header to variable, and remove from line
			$name = $row[0];
			array_shift($row);

			// where the crime headers are in the document, line 4 and 5
			if ($rowCount === 4 || $rowCount === 5) {
				$header = $row;
			}

			// beware! here be dragons, and data
			else if ($rowCount >= 7) {
				array_combine($header, $row);

				if ($name != '') {
					// If we're dealing with a region (Wales is considered one)
					// Also exclude England as there's a section called England and Wales
					if (stristr($name, 'region') || stristr($name, 'wales')) {
						$region = $xml->createElement("region");
						$region_id = preg_replace('/ Region/', '', $name);
						$region_id = preg_replace('/\s/', '_', $region_id);
						$region->setAttribute('id', $region_id);
						$root->appendChild($region);
					}
					else {
						// var_dump($name);
						// $areas[$name] = $areaXml->appendChild("<area></area>");
					}

					//no useful data comes out after this
					if ($name === 'ENGLAND AND WALES') break;
				}
			}
		}
		fclose($fileHandle);
	}

	//indentation is pretty great
	$xml->formatOutput = true;

	file_put_contents(__DIR__. '/data/crime_data.xml', $xml->saveXML());
	return $xml;
}

print_r(twoColCsvToXml('./data/data.csv'));
?>
