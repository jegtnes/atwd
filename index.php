<pre>
<?php
function twoColCsvToArray($filename) {
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$header = null;
	$data = array();
	$handle = fopen($filename, 'r');

	if ($handle) {
		while ($row = fgetcsv($handle, 0, ',')) {

			//assign header name
			$name = $row[0];

			//remove header name from data
			array_shift($row);

			//if first iteration of loop, we're on headers
			if(!$header) {
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
print_r(twoColCsvToArray('./data/dawg.csv'));
?>
</pre>
