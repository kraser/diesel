<?php 

//phpinfo();

$object = new stdClass();
$array = array(1, 'var_dump test', 4 => $object);
var_dump($array);

ini_set('html_errors', 'On');

echo filter_input ( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );

echo 'Xdebug'

?>