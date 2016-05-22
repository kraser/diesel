<?php

/**
 * Description of FileTools
 *
 * @author kraser
 */
class FileTools
{

    public static function init ()
    {
        if ( defined ( "CACHE" ) )
            self::createDir ( DOCROOT . DS . CACHE );

        if ( defined ( "DATA" ) )
            self::createDir ( DOCROOT . DS . DATA );

        if ( defined ( "DATA" ) )
            self::createDir ( DOCROOT . DS . IMGS );
    }

    /**
     * <pre>Рекурсивно сканирует заданный каталог</pre>
     * @param String $dir <p>Заданный для сканирования каталог</p>
     * @return Array
     */
    public static function scanDirRecursive ( $dir )
    {
        $dir = realpath ( $dir );
        $result = new CmsFolder ( $dir );
        if ( !$dir )
            return $result;

        $rows = scandir ( $dir );
        foreach ( $rows as $row )
        {
            if ( $row == '.' || $row == '..' )
                continue;

            $pathName = $dir . DS . $row;
            // каталоги для дальнейшей рекурсии
            if ( is_dir ( $pathName ) )
                $result->folders[$row] = self::scanDirRecursive ( $pathName );

            if ( is_file ( $pathName ) )
                $result->files[basename ( $pathName )] = $pathName;
        }

        return $result;
    }

    /**
     * <pre>Создает директорию <b>$dirName</b></pre>
     * @param String $dirName <p>Имя директории</p>
     */
    public static function createDir ( $dirName )
    {
        if ( !file_exists ( $dirName ) )
            mkdir ( $dirName, 0777 );
    }

    /**
     * <pre>Копирует файл источника в файл назначения</pre>
     * @param String $source <p>Имя файла источника</p>
     * @param String $destination <p>Имя файла назначения</p>
     * @param Boolean $remove <p>Флаг удаления источника</p>
     * @return void
     */
    public static function copyFile ( $source, $destination, $remove = true )
    {
        if ( $source == $destination || !file_exists ( $source ) )
            return;

        if ( file_exists ( $destination ) )
        {
            $hashSource = md5 ( file_get_contents ( $source ) );
            $hashDestination = md5 ( file_get_contents ( $destination ) );
            if ( $hashDestination == $hashSource )
                return;
            else
                self::removeFile ( $destination );
        }

        if ( copy ( $source, $destination ) )
        {
            //self::removeFile ( $source );
        }

        chmod ( $destination, 0777 );
    }

    public static function removeFile ( $fileName )
    {
        if ( !file_exists ( $fileName ) )
            return;
        elseif ( is_file ( $fileName ) )
            unlink ( $fileName );
        elseif ( is_dir ( $fileName ) )
        {
            $folders = self::scanDirRecursive ( $fileName );
            foreach ( $folders->files as $file )
            {
                unlink ( $folders->pathName . DS . $file );
            }
            foreach ( $folders->folders as $folder )
            {
                self::removeFile ( $folder->pathName );
            }
            rmdir ( $fileName );
        }
    }

    public static function getPhpUploadError ( $errorCode )
    {
        switch ( $errorCode )
        {
            case UPLOAD_ERR_OK:
                return "Без ошибки";
            case UPLOAD_ERR_INI_SIZE:
                return "Размер файла больше " . ini_get ( 'upload_max_filesize' );
            case UPLOAD_ERR_FORM_SIZE:
                return "Размер файла больше MAX_FILE_SIZE";
            case UPLOAD_ERR_PARTIAL:
                return "Файл загрузился неполностью";
            case UPLOAD_ERR_NO_FILE:
                return "Файл не загрузился";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "На сервере отсутствует папка для временных файлов";
            case UPLOAD_ERR_CANT_WRITE:
                return "Дисковая ошибка записи";
            case UPLOAD_ERR_EXTENSION:
                return "Загрузка файлов на сервер запрещена";
            default:
                return "Ошибка #$errorCode";
        }
    }
}

class CmsFolder
{
    public $pathName;
    public $folders;
    public $files;

    public function __construct ( $dir )
    {
        $this->pathName = $dir;
        $this->folders = array ();
        $this->files = array ();
    }
}
