<pre>
<?php
if (($file = fopen("data/data.csv", "r")) === true) {
    $data = fgetcsv($file);
    var_dump($data);
    fclose($file);
}
?>
</pre>
