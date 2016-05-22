<?php
ini_set ( 'display_errors', 0 );
error_reporting ( ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
mb_internal_encoding(_CHARSET);

require_once ( TOOLS . DS . '_tools.php' );
require_once ( TOOLS . DS . 'deprecatedTools.php' );
require_once ( TOOLS . DS . 'systemFunctions.php' );
require_once ( TOOLS . DS . 'additionalFunctions.php' );
require_once ( TOOLS . DS . 'media.php' );
require_once ( TOOLS . DS . 'comments.php' ); // здесь возможно сразу обращение в базу
require_once ( LIBS . DS . 'Smarty/Smarty.class.php' );
require_once ( CORE. DS . 'Starter' . EXT );
require_once ( TOOLS . DS . 'Functions.php' );

