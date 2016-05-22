<?php
$base = require __DIR__ . DS . "base.php";
$production = include DOCROOT . DS . SITE . DS . "config" . DS . "config.php";
$develop = include __DIR__ . DS . "develop.php";

return ArrayTools::merge ( $base, $production, $develop );

