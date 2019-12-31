<?php
$serverName = gethostname ();
$serverConfig = __DIR__ . DS . "develop/$serverName.php";
if ( file_exists ( $serverConfig ) )
    $develop = require ( $serverConfig );
else
    $develop = [];

$hostName = filter_input ( INPUT_SERVER, 'HTTP_HOST' );
if ( !ini_get ( 'open_basedir' ) && file_exists ( '/srv/' . $hostName . '.php' ) )
{
    $config = include '/srv/' . $hostName . '.php';
    $develop = ArrayTools::merge ( $develop, $config );
}

return $develop;