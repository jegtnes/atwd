<pre>
<?php
require ("../library/utilities.php");

if (file_exists(DATA_SOURCE)) {
	$xml = simplexml_load_file(DATA_SOURCE);
	$request = parseApiRequest($_SERVER['REQUEST_URI']);
}

?>
</pre>
