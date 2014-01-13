<pre>
<?php
ini_set('auto_detect_line_endings', true);
function twoColCsvToXml($filename) {
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$rowCount = 0;

	$data = array();
	$handle = fopen($filename, 'r');

	if ($handle) {
		while ($row = fgetcsv($handle, 0, ',')) {
			$rowCount++;

			$name = $row[0];
			array_shift($row);

			if ($rowCount === 4 || $rowCount === 5) {
				$header = $row;
			}

			else if ($rowCount >= 7) {
				//array_filter removes empty values in array
				$data[$name] = array_filter(
					array_combine($header, $row)
				);

			}
		}
		fclose($handle);
	}


	return $data;
}
print_r(twoColCsvToXml('./data/data.csv'));
?>
</pre>
