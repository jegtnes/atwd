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

	// check whether we've got the right year. TODO: implement proper XML error
	if ($params[2] != '6-2013') return false;

	// Let's remove irrelevant parameters. First is the intial /, second
	// is 'crimes', which is just the API call URL, third is the year,
	// 6-2013. If this isn't the case we're returning false above anyhow,
	// so from this point on the structure should be reasonably correct.
	else $params = array_slice($params, 3);

	$return = [];
	$count = 0;

	if ($params[0] === 'xml' || $params[0] === 'json') {
		$return['file_type'] = $params[0];
	}

	else if ($params[0] === 'put' || $params[0] === 'post' || $params[0] === 'delete') {
		$return['verb'] = $params[0];
	}

	else {
		$return['region'] = $params[0];
		$return['verb'] = 'get';
		$return['file_type'] = $params[1];
	}

	/*
	if first equals file type, show totals
	elseif first equals verb, region is after
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

	return $return;
}
?>
