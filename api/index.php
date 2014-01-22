<?php
$queryString = explode('&', $_SERVER['QUERY_STRING']);
$qs = [];
foreach($queryString as $query) {
	$strings = explode('=', $query);
	$qs[$strings[0]] = $strings[1];
}

print_r($qs);
?>
