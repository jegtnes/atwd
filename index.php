<?php
header('Content-Type: text/xml');
// needed to work around issues with OS X/Excel line endings
ini_set('auto_detect_line_endings', true);

function twoColCsvToXml($filename) {
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$rowCount = 0;

	$data = array();

	$xml = new DOMDocument;

	$areas = [];

	$root = $xml->createElement('crimes');
	$root = $xml->appendChild($root);
	$fileHandle = fopen($filename, 'r');

	if ($fileHandle) {
		while ($row = fgetcsv($fileHandle, 0, ',')) {
			$rowCount++;

			// dealing with two sets of headers here
			// add area header to variable, and remove from line
			$name = $row[0];
			$total = preg_replace('/,/', '', $row[1]);
			array_shift($row);

			// where the crime headers are in the document, line 4 and 5
			if ($rowCount === 4 || $rowCount === 5) {
				$header = $row;
			}

			// beware! here be dragons, and data
			else if ($rowCount >= 7) {
				array_combine($header, $row);

				if ($name != '' && $name != 'ENGLAND AND WALES') {

					// If we're dealing with a region (Wales is considered one)
					// so is 'Action Fraud1' (badly formatted) and BTP.
					if (stristr($name, 'region') || $name == 'WALES') {
						$region = $xml->createElement("region");
						//remove Region from name, or '1' from Action Fraud
						$region_id = preg_replace('/( Region)|1/', '', ucwords(strtolower($name)));

						// format things according to spec and add:
						$region->setAttribute('id', $region_id);

						$region->setAttribute('total', $total);

						$region = $root->appendChild($region);


						/* areas are defined to come before their respective regions
						 * this works on the assumption of this
						 * once we reach a region, add children to the region
						 * and unset the areas, to let areas from the
						 * next region be allocated to the array */
						foreach ($areas as $area) {
							$region->appendChild($area);
						}
						unset($areas);
					}

					// dealing with Nationals
					else if (preg_match('/^(Action Fraud1|British Transport Police)$/', $name)) {
						$national = $xml->createElement("national");

						// Remove 1 from Action Fraud 1
						$national_id = preg_replace('/1/', '', $name);
						$national->setAttribute('id', $national_id);

						$national->setAttribute('total', $total);

						$root->appendChild($national);
					}

					// Will cover all areas. Needs to exclude England as that gets added
					else if ($name != 'ENGLAND') {
						$areas[$name] = $xml->createElement("area");
						$areas[$name]->setAttribute('id', $name);

						$areas[$name]->setAttribute('total', $total);
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
	return $xml->saveXML();
}

print_r(twoColCsvToXml('./data/data.csv'));
?>
