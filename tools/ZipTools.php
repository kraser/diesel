<?php

/**
 * Description of ZipTools
 *
 * @author kraser
 */
class ZipTools
{

    /**
     * <pre>Проверяет имя файла на соответствие шаблону имени архивного файла</pre>
     * @param String $filename <p>Имя файла</p>
     * @return Boolean
     */
    public static function isZipArchive ($filename)
    {
        return preg_match ('/.+\.zip$/', $filename);
    }

    /**
     * <pre>Распаковывает файл, удаляет архив и возвращает массив имен распакованных файлов</pre>
     * @param String $zipname <p>Имя архивного файла</p>
     * @param String $dir <p>Папка, куда распаковывать файлы</p>
     * @return Array
     */
    public static function unzipFiles($zipname, $dir)
    {
        if ( !file_exists ( $dir ) )
        {
            @mkdir ( $dir, 0777, true ) or self::notify ( "Ошибка создания директории $dir" );
        }

        $zip = new ZipArchive();
        $result = $zip->open ( $zipname );
        if ( $result !== true )
        {
            self::notify ( "Ошибка открытия zip-архива $zipname: $result" );
        }

        $files = array ();
        for ( $i = 0; $i < $zip->numFiles; $i++ )
        {
            $entry = $zip->statIndex ( $i );

            //$fileNameDOS = @iconv ( 'CP866', 'UTF-8//TRANSLIT', $entry['name'] );
            //$fileNameUTF = $entry['name'];
            $fileName = $entry['name'];//self::translit ( (strlen ( $fileNameDOS ) > strlen ( $fileNameUTF ) ? $fileNameDOS : $fileNameUTF ) );
            $localName = $dir . '/' . $fileName;

            $zipStream = $zip->getStream ( $entry['name'] );
            $outputStream = fopen ( $localName, "wb+" );
            while ( !feof ( $zipStream ) )
            {
                fputs ( $outputStream, fread ( $zipStream, 4096 ) );
            }

            fclose ( $zipStream );
            fclose ( $outputStream );

            $files[$fileName] = $localName;
        }

        $zip->close();

        return $files;
    }

    /**
     * <pre>Переноcит файлы в архив</pre>
     * @param String $zipname <p>Имя создаваемого файла архива</p>
     * @param Array $files <p>Массив имён файлов для архивирования</p>
     */
    public static function zipFiles ($zipname, $files)
    {
        $path = dirname ($zipname);
        if (!file_exists ($path))
        {
            $oldUmask = umask ( 0 ); // Чтобы вся иерархия созданных директорий получила полный доступ ( 0777, а не 0777~umask)!
            @mkdir ( $path, 0777, true ) or self::notify ( "Ошибка создания архивной директории $path: $php_errormsg" );
            umask ( $oldUmask );
        }
        $zip = new ZipArchive();
        $zip->open ($zipname, ZipArchive::OVERWRITE) or self::notify ( "Ошибка создания zip-архива $zipname: ".$zip->getStatusString () );

        foreach ( $files as $fileName => $localName )
        {
            if (is_numeric ($fileName))
            {
                $fileName = $localName;
            }

            $zipName = basename ( $fileName );
            $zip->addFile ( $localName, $zipName ) or self::notify ("Ошибка добавления файла $localName в zip-архив $zipname: ".$zip->getStatusString());
        }
        $zip->close ();
    }

    public static function translit ($text)
    {
        $cyr = array
        (
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ы', 'Ъ', 'Э', 'Ю', 'Я'
        );
        $lat = array
        (
            'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya',
            'A', 'B', 'V', 'G', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'TS', 'CH', 'SH', 'SCH', '', 'Y', '', 'E', 'YU', 'YA'
        );

        return preg_replace('/[^\.A-z0-9]+/', '_', str_replace ($cyr, $lat, $text));
    }

    private static function notify ( $message )
    {
        throw new Exception ( $message );
    }
}
