<?php

require_once __DIR__ . "/../define.php";
$stamp = time ();
$fileName = "Update_$stamp.sql";
if ( !file_exists ( MIGRATE ) )
{
    mkdir ( MIGRATE, 0777 );
}
$fp = fopen ( MIGRATE . DS . $fileName, "w+" );
fputs ( $fp, "-- update for timestamp $stamp\n" );
fclose ( $fp );
echo "Created migration $fileName\n";
