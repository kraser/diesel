<?php
//update for timestamp 1459449480
//$testTable = "test";
//$testCreate = "CREATE TABLE `{test_table}` LIKE `prefix_gallery`";
//$testCreateSql = str_replace ( "{test_table}", $testTable, $testCreate );
//$testDrop = "DROP TABLE IF EXISTS {test_table}";
//$testDropSql = str_replace ( "{test_table}", $testTable, $testDrop );
$procedure = "CREATE PROCEDURE addcol() BEGIN
IF NOT EXISTS(
    SELECT * FROM information_schema.COLUMNS
    WHERE COLUMN_NAME='video' AND TABLE_NAME='{the_table_name}' AND TABLE_SCHEMA='{the_schema_name}'
    )
    THEN
    ALTER TABLE `{the_schema_name}`.`{the_table_name}`
    ADD COLUMN `video` VARCHAR(255) NOT NULL DEFAULT '';

END IF;
END;
";
$callSql = "CALL addcol()";
$dropProcedureSql = "DROP PROCEDURE IF EXISTS addcol";

$dbConf = Starter::app ()->db;
$schema = $dbConf['dbName'];
try
{
//    SqlTools::execute ( $testDropSql );
//    SqlTools::execute ( $testCreateSql );
//    $testProcedureSql = str_replace("{the_schema_name}", $schema, str_replace("{the_table_name}", $testTable, $procedure) );
//    SqlTools::execute ( $testProcedureSql );
//    SqlTools::execute ( $callSql );
//    SqlTools::execute ( $dropProcedureSql );
//    SqlTools::execute ( $testDropSql );

    $procedureSql = str_replace("{the_schema_name}", $schema, str_replace("{the_table_name}", "prefix_images", $procedure) );
    SqlTools::execute ( $procedureSql );
    SqlTools::execute ( $callSql );
    SqlTools::execute ( $dropProcedureSql );
}
catch (Exception $ex)
{
    SqlTools::execute ( $dropProcedureSql );
    //SqlTools::execute ( $testDropSql );
    throw $ex;
}
