<pre>
<?php
require ("../library/utilities.php");
function object_merge($o1, $o2)
{
return (object) array_merge((array) $o1, (array) $o2);
}
if (file_exists(DATA_SOURCE)) {
	$xml = simplexml_load_file(DATA_SOURCE);
	$request = parseApiRequest($_SERVER['REQUEST_URI']);

	foreach ($xml->region as $x) {
		var_dump($x['total']);
		var_dump($x['id']);
	}

	foreach ($xml->national as $x) {
		var_dump($x['total']);
		var_dump($x['id']);
	}
}
?>
</pre>
