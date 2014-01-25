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

	// check whether we've got the right year. TODO: implement proper XML error
	if ($params[1] != '2013-6') return false;

	$return = [];
	$count = 0;

	foreach($params as $param) {

		if ($param === end($params)) {
			if ($param === 'xml') $return['requestFormat'] = 'xml';
			else if ($param === 'json') $return['requestFormat'] = 'json';
			else {
				$return['requestFormat'] = false;
			}
		}
		/*
		if first equals file type, show totals
		else if first equals verb, region is after
		     if put, :XXXXX is after region, but same parameter
		     else if post, region is alone, and specific numbers come after in a param
		     else if delete, file type is after region, and that's it
		else it is GET region
		/*

		*/
		// atwd/crimes/6-2013/xml
		// atwd/crimes/6-2013/south_west/xml
		// atwd/crimes/6-2013/put/british_transport_police:51970/xml
		// atwd/crimes/6-2013/post/south_west/wessex/hom:4-vwi:15-vwoi:25/xml
		// atwd/crimes/6-2013/delete/wessex/xml

		if ($count === 2) {
			if ($param === 'put') {
				$return['parameter'] = 'put';
			}

			else if ($param === 'post') {
				$return['parameter'] = 'post';
			}

			else if ($param === 'delete') {
				$return['parameter'] = 'delete';
			}

			else {
				$return['parameter'] = 'get';
			}

		}
		$count++;
	}

	return $return;
}
?>
