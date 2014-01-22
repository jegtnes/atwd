<?php

/* Function to split a query string into an associative array
 * e.g. ?var1=true&var2=false&var3=xml returns:
 * [var1 => true, var2 => false, var3 => xml]
 */
function splitQueryString($queryString) {
	$queryStrings = explode('&', $queryString);
	$queryArray = [];
	foreach($queryStrings as $queryString) {
		$query = explode('=', $queryString);
		$queryArray[$query[0]] = $query[1];
	}
	return $queryArray;
}

function parseApiRequest($url) {
	$params = explode('/', $url);

	// the first parameter will be the initial /, remove it
	array_shift($params);

	$return = [];
	$count = 0;
	foreach($params as $param) {
		$count++;
		if ($count >2) {
			$return[] = $param;

		}
	}

	return $return;
}
?>
