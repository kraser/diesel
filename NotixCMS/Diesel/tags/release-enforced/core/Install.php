<?php

/**
 * Класс для запуска инсталлятора базы и инсталляторов и апдейтеров всех существующих компонентов
 */
class Install extends CmsComponent
{
    private $dbConf;

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $this->dbConf = Starter::app ()->db;
    }

    public function Run ()
    {
        if ( $this->isNoTables () )
            $this->initialInstallDb ();

        $this->updateDb ();
    }

    private function isNoTables ()
    {
        $dbName = $this->dbConf['dbName'];
        $tables = SqlTools::selectRows ( "SHOW TABLES IN `$dbName`", MYSQL_ASSOC );
        if ( count ( $tables ) )
            return false;
        return true;
    }

    private function initialInstallDB ()
    {
        $path = realpath ( INSTALL );
        $queue =
        [
            'migration',
            'admin_users',
            'settings',
            'images',
            'images_storage',
            'mediafiles',
            'seo',
            'data',
            'blocks',
            'comments'
        ];

        foreach ( $queue as $table )
        {
            if ( $this->isTableExists ( $table ) )
                continue;

            if ( file_exists ( $path . DS . $table . ".sql" ) )
                $this->executeSql ( $path . DS . $table . ".sql" );
            if ( file_exists ( $path . DS . $table . "_data.sql" ) )
                $this->executeSql ( $path . DS . $table . "_data.sql" );
        }
    }

    private function updateDb ()
    {
        $installedModule = SqlTools::selectRows ( "SELECT `module` FROM `prefix_migration` WHERE `module`!=''", MYSQL_ASSOC, "module" );
        $migrations = SqlTools::selectObjects ( "SELECT * FROM `prefix_migration`", null, "stamp" );
        $toUpdate = [];
        $modules = array_keys ( Starter::app ()->modules );
        foreach ( $modules as $alias )
        {
            $moduleDbData = FileTools::scanDirRecursive ( Starter::getAliasPath ( $alias ) . DS . 'dbData' );
            if ( !$moduleDbData->pathName )
                continue;

            $toUpdate = ArrayTools::merge ( $toUpdate, $moduleDbData->folders['migration']->files );
            if ( array_key_exists ( $alias, $installedModule ) )
                continue;

            $installDir = $moduleDbData->folders['install'];
            if ( $installDir->pathName )
                $config = require $installDir->files['config.php'];
            else
                $config = [];

            foreach ( $config as $fileName )
            {
                $this->executeSql ( $installDir->files[$fileName] );
            }
            $query = "INSERT into `prefix_migration` (`stamp`, `module` ) VALUES ('" . time () . "', '$alias')";
            SqlTools::insert ( $query );
        }

        $systemUpdate = FileTools::scanDirRecursive ( MIGRATE );
        $toUpdate = ArrayTools::merge ( $toUpdate, $systemUpdate->files );
        foreach ( $toUpdate as $file )
        {
            $pathInfo = explode ( "_", pathinfo ( $file, PATHINFO_FILENAME ) );
            $stamp = array_pop ( $pathInfo );
            if ( array_key_exists ( $stamp, $migrations ) )
                continue;

            $this->executeMigration ( $file, $stamp );
        }
    }

    private function isTableExists ( $table )
    {
        try
        {
            $result = SqlTools::execute ( "SELECT 1 FROM `prefix_$table`" );
        }
        catch ( CmsException $ex )
        {
            if ( $ex->getCode () === 1146 )
                $result = false;
            else
                throw $ex;
        }

        return $result;
    }

    private function executeSql ( $fileName )
    {
        $query = file_get_contents ( $fileName );
        $result = SqlTools::execute ( $query );
        return $result;
    }

    private function executeMigration ( $fileName, $stamp )
    {
        $ext = pathinfo ( $fileName, PATHINFO_EXTENSION );
        $result = $ext == "sql" ? $this->executeSql ( $fileName ) : $this->executePhpMigration ( $fileName );

        if ( $result )
            SqlTools::insert ( "INSERT INTO `prefix_migration` (`stamp`) VALUES ('$stamp')" );
    }
    
    private function executePhpMigration ( $fileName )
    {
        return require $fileName;
    }
}