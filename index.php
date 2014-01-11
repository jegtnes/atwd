<pre>
<?php
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

			//assign header name
			$name = $row[0];

			//remove header name from data
			array_shift($row);


			if ($rowCount === 1) {
				$header = $row;
			}

			else {
				$data[$name] = array_combine($header, $row);
			}
		}
		fclose($handle);
	}


	return $data;
}
print_r(twoColCsvToXml('./data/dawg.csv'));
?>
</pre>
