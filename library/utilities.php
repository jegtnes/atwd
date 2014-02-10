<?php

define('DATA_SOURCE', dirname(__DIR__) . '/data/crime_data.xml');

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

/* Function to turn mixed data containing arrays and objects to array.
 * Found on https://coderwall.com/p/8mmicq
 */
function object_to_array($d) {
    if (is_object($d))
        $d = get_object_vars($d);

    return is_array($d) ? array_map(__METHOD__, $d) : $d;
}

/* Function to turn mixed data containing arrays and objects to array.
 * Found on https://coderwall.com/p/8mmicq
 */
function array_to_object($d) {
    return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
}

/* json_encode from XML results in XML element attributes turned into an array
 * called @attributes. If you merely want these attributes applied to the parent
 * as attribute-value pairs instead, this function will accomplish exactly that.
 * Only works with pure arrays, so make sure you convert it to an array first. */
function extractJsonAttributes($json_array = array()) {
	$data = [];

	foreach ($json_array as $key => $value) {

		// where the magic happens. Take things one level up
		// going towards the base case of having no @attributes
		if ($key === '@attributes') {
			foreach ($value as $attributeKey => $attributeValue) {
				$data[$attributeKey] = $attributeValue;
			}
		}

		// recursion! go one level deeper to keep checking for attributes
		else if (is_array($value)) {
			$data[$key] = extractJsonAttributes($value);
		}

		else {
			$data[$key] = $value;
		}
	}
	return $data;
}

function parseApiRequest($url) {
	$params = explode('/', $url);

	$return = [];

	// check whether we've got the right year. TODO: implement proper XML error
	if ($params[2] == '6-2013')  {
		$return['year'] = $params[2];
	}
	else return false;

	// Let's remove irrelevant parameters. First is the intial /, second
	// is 'crimes', which is just the API call URL, third is the year,
	// 6-2013. If this isn't the case we're returning false above anyhow,
	// so from this point on the structure should be reasonably correct.
	$params = array_slice($params, 3);

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
