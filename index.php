<?php
header('Content-Type: text/xml');
// needed to work around issues with OS X/Excel line endings
ini_set('auto_detect_line_endings', true);

// Cleans up the unclean CSV values, removing the comma
function clean_value($val) {
	return preg_replace('/,/', '', $val);
}

function twoColCsvToXml($filename) {
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$rowCount = 0;

	$data = array();

	$xml = new DOMDocument('1.0', 'utf-8');

	$areas = [];

	$root = $xml->createElement('crimes');
	$root = $xml->appendChild($root);

	//kudos to VolkerK at StackOverflow for helping me figure this out:
	// http://stackoverflow.com/questions/2236931/php-dom-xml-create-multiple-namespace-attributes
	$root->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
	$root->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance' ,'noNamespaceSchemaLocation', 'crime_schema.xsd');
	$fileHandle = fopen($filename, 'r');

	if ($fileHandle) {
		while ($row = fgetcsv($fileHandle, 0, ',')) {
			$rowCount++;

			$total                                                            = clean_value($row[1]);
			$total_excluding_fraud                                            = clean_value($row[2]);

			$crimes['violence_against_the_person']['total']                   = clean_value($row[4]);
			$crimes['violence_against_the_person']['homicide']                = clean_value($row[5]);
			$crimes['violence_against_the_person']['violence_with_injury']    = clean_value($row[6]);
			$crimes['violence_against_the_person']['violence_without_injury'] = clean_value($row[7]);

			$crimes['sexual_offences']                                        = clean_value($row[8]);
			$crimes['robbery']                                                = clean_value($row[9]);

			$crimes['theft_offences']['total']                                = clean_value($row[10]);

			$crimes['theft_offences']['burglary']['total']                    = clean_value($row[11]);
			$crimes['theft_offences']['burglary']['domestic_burglary']        = clean_value($row[12]);
			$crimes['theft_offences']['burglary']['nondomestic_burglary']     = clean_value($row[13]);

			$crimes['theft_offences']['vehicle_offences']                     = clean_value($row[14]);
			$crimes['theft_offences']['theft_from_the_person']                = clean_value($row[15]);
			$crimes['theft_offences']['bicycle_theft']                        = clean_value($row[16]);
			$crimes['theft_offences']['shoplifting']                          = clean_value($row[17]);
			$crimes['theft_offences']['all_other_theft_offences']             = clean_value($row[18]);

			$crimes['criminal_damage_and_arson']                              = clean_value($row[19]);
			$crimes['drug_offences']                                          = clean_value($row[21]);
			$crimes['possession_of_weapons_offences']                         = clean_value($row[22]);
			$crimes['public_order_offences']                                  = clean_value($row[23]);
			$crimes['misceanellous_crimes_against_society']                   = clean_value($row[24]);
			$crimes['fraud']                                                  = clean_value($row[26]);

			// as we're dealing with two sets of headers here
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

						foreach($crimes as $crime_name => $crime_value) {

							// If the crime is in a category marked by a total, it'll be in a subarray
							if (is_array($crime_value)) {
								$element = $xml->createElement("category");

								// The problem here is with adding the third level of category onto the second. Seems like it's ignored currently

								foreach($crime_value as $subcat => $subcat_value) {
									if ($subcat === 'total') {
										$element->setAttribute("name", $crime_name);
										$element->setAttribute($subcat, $subcat_value);
									}
									else {
										if (is_array($subcat_value)) {
											$elementTwo = $xml->createElement("category");
											foreach($subcat_value as $subcat_subcat => $subcat_subcat_value) {
												if ($subcat_subcat === "total") {
													$elementTwo->setAttribute("name", $subcat);
													$elementTwo->setAttribute($subcat_subcat, $subcat_subcat_value);
												}
												else {
													$subelement = $xml->createElement("crime");
													$subelement->setAttribute("name", $subcat_subcat);
													$subelement->setAttribute("total", $subcat_subcat_value);
													$elementTwo->appendChild($subelement);
												}
											}
											$element->appendChild($elementTwo);
										}

										else {
											$subelement = $xml->createElement("crime");
											$subelement->setAttribute("name", $subcat);
											$subelement->setAttribute("total", $subcat_value);
											$element->appendChild($subelement);
										}
									}
								}
							}
							// If not, proceed with single elements as normal
							else {
								$element = $xml->createElement("crime");
								$element->setAttribute("name", $crime_name);
								$element->setAttribute("total", $crime_value);
							}
							$region->appendChild($element);
						}

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

							foreach($crimes as $crime_name => $crime_value) {

								// If the crime is in a category marked by a total, it'll be in a subarray
								if (is_array($crime_value)) {
									$element = $xml->createElement("category");

									// The problem here is with adding the third level of category onto the second. Seems like it's ignored currently

									foreach($crime_value as $subcat => $subcat_value) {
										if ($subcat === 'total') {
											$element->setAttribute("name", $crime_name);
											$element->setAttribute($subcat, $subcat_value);
										}
										else {
											if (is_array($subcat_value)) {
												$elementTwo = $xml->createElement("category");
												foreach($subcat_value as $subcat_subcat => $subcat_subcat_value) {
													if ($subcat_subcat === "total") {
														$elementTwo->setAttribute("name", $subcat);
														$elementTwo->setAttribute($subcat_subcat, $subcat_subcat_value);
													}
													else {
														$subelement = $xml->createElement("crime");
														$subelement->setAttribute("name", $subcat_subcat);
														$subelement->setAttribute("total", $subcat_subcat_value);
														$elementTwo->appendChild($subelement);
													}
												}
												$element->appendChild($elementTwo);
											}

											else {
												$subelement = $xml->createElement("crime");
												$subelement->setAttribute("name", $subcat);
												$subelement->setAttribute("total", $subcat_value);
												$element->appendChild($subelement);
											}
										}
									}
								}
								// If not, proceed with single elements as normal
								else {
									$element = $xml->createElement("crime");
									$element->setAttribute("name", $crime_name);
									$element->setAttribute("total", $crime_value);
								}
								$national->appendChild($element);
							}


						$root->appendChild($national);
					}

					// Will cover all areas. Needs to exclude England as that gets added
					else if ($name != 'ENGLAND') {
						$areas[$name] = $xml->createElement("area");
						$areas[$name]->setAttribute('id', $name);
						$areas[$name]->setAttribute('total', $total);

						foreach($crimes as $crime_name => $crime_value) {

							// If the crime is in a category marked by a total, it'll be in a subarray
							if (is_array($crime_value)) {
								$element = $xml->createElement("category");

								foreach($crime_value as $subcat => $subcat_value) {
									if ($subcat === 'total') {
										$element->setAttribute("name", $crime_name);
										$element->setAttribute($subcat, $subcat_value);
									}
									else {
										// if we've got an array inside an array
										// arrayception
										// this is just for burglary inside theft offences
										if (is_array($subcat_value)) {
											$elementTwo = $xml->createElement("category");
											foreach($subcat_value as $subcat_subcat => $subcat_subcat_value) {
												if ($subcat_subcat === "total") {
													$elementTwo->setAttribute("name", $subcat);
													$elementTwo->setAttribute($subcat_subcat, $subcat_subcat_value);
												}
												else {
													$subelement = $xml->createElement("crime");
													$subelement->setAttribute("name", $subcat_subcat);
													$subelement->setAttribute("total", $subcat_subcat_value);
													$elementTwo->appendChild($subelement);
												}
											}

											// add the above to the above category
											$element->appendChild($elementTwo);
										}

										else {
											$subelement = $xml->createElement("crime");
											$subelement->setAttribute("name", $subcat);
											$subelement->setAttribute("total", $subcat_value);
											$element->appendChild($subelement);
										}
									}
								}
							}
							// If not, proceed with single elements as normal
							else {
								$element = $xml->createElement("crime");
								$element->setAttribute("name", $crime_name);
								$element->setAttribute("total", $crime_value);
							}
							$areas[$name]->appendChild($element);
						}
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
