<?php
//update for timestamp 1459452123
//$testTable = "test";
//$testCreate = "CREATE TABLE `{test_table}` LIKE `prefix_gallery`";
//$testCreateSql = str_replace ( "{test_table}", $testTable, $testCreate );
//$testDrop = "DROP TABLE IF EXISTS {test_table}";
//$testDropSql = str_replace ( "{test_table}", $testTable, $testDrop );
$procedure = "CREATE PROCEDURE dropcol() BEGIN
IF EXISTS(
    SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='video' AND TABLE_NAME='{the_table_name}' AND TABLE_SCHEMA='{the_schema_name}'
        )
        THEN
        ALTER TABLE `{the_schema_name}`.`{the_table_name}`
        DROP COLUMN `video`;
END IF;
END;
";
$callSql = "CALL dropcol()";
$dropProcedureSql = "DROP PROCEDURE IF EXISTS dropcol";

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

    $procedureSql = str_replace("{the_schema_name}", $schema, str_replace("{the_table_name}", "prefix_gallery", $procedure) );
    SqlTools::execute ( $procedureSql );
    SqlTools::execute ( $callSql );
    SqlTools::execute ( $dropProcedureSql );
}
catch (Exception $ex)
{
    SqlTools::execute ( $dropProcedureSql );
//    SqlTools::execute ( $testDropSql );
    throw $ex;
}
