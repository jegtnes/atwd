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

	if ($params[0] === 'xml' || $params[0] === 'json') {
		$return['response_format'] = $params[0];
		$return['region'] = 'england_and_wales';
		$return['verb'] = 'get';
	}

	else if ($params[0] === 'put' || $params[0] === 'post' || $params[0] === 'delete') {
		$return['verb'] = $params[0];

		if ($params[0] === 'put') {
			$put = explode(':', $params[1]);
			$return['region'] = $put[0];
			$return['update_amount'] = $put[1];
			$return['response_format'] = $params[2];
		}

		else if ($params[0] === 'post') {
			$return['region'] = $params[1];
			$return['area'] = $params[2];
			$values = explode('-', $params[3]);
			foreach($values as $value) {
				$split = explode(":", $value);
				$return['crime_values'][$split[0]] = $split[1];
			}
			$return['response_format'] = $params[4];
		}

		else if ($params[0] === 'delete') {
			$return['region'] = $params[1];
			$return['response_format'] = $params[2];
		}
	}

	else {
		$return['region'] = $params[0];
		$return['verb'] = 'get';
		$return['response_format'] = $params[1];
	}

	return $return;
}
?>
