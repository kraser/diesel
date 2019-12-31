<?php
$base = require __DIR__ . DS . "base.php";
if ( file_exists ( DOCROOT . DS . SITE . DS . "config" . DS . "config.php" ) )
{
    $production = include DOCROOT . DS . SITE . DS . "config" . DS . "config.php";
}
else if ( file_exists ( __DIR__ . DS . "diesel.php" ) )
{
    $production = include __DIR__ . DS . "diesel.php";
}
else
{
    $production = [];
}
$develop = include __DIR__ . DS . "develop.php";

return ArrayTools::merge ( $base, $production, $develop );

