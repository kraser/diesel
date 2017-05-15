<?php
ini_set ( 'display_errors', 'On' );
error_reporting ( E_ALL & ~E_DEPRECATED );
//mb_internal_encoding(_CHARSET);

require_once ( TOOLS . DS . '_tools.php' );
require_once ( TOOLS . DS . 'deprecatedTools.php' );
require_once ( TOOLS . DS . 'DevHelper.php' );
require_once ( TOOLS . DS . 'systemFunctions.php' );
require_once ( TOOLS . DS . 'additionalFunctions.php' );
require_once ( TOOLS . DS . 'comments.php' );
require_once ( LIBS . DS . 'Smarty/Smarty.class.php' );
require_once ( CORE. DS . 'Starter' . EXT );
