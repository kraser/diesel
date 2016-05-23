<?php
/**
 * Description of Imager
 *
 * @author kraser
 */
class Imager extends CmsComponent
{
    private $alldata;
    private $allowedExts = [ 'gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff' ];
    private $preparedImgs;
    private $defaultImage;

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $themeDefaultImage = DS . SITE . DS . Starter::app ()->getTheme () . DS . 'assets' . DS . 'images' . DS . 'nophoto.png';
        $cmsDefaultImage = DS . 'images' . DS . 'default.png';
        $this->defaultImage = file_exists ( DOCROOT . $themeDefaultImage ) ? $themeDefaultImage : $cmsDefaultImage;
    }

    /**
     * <pre>Добавляет изображение в хранилище</pre>
     * @param String $file <p>Имя загружаемого файла</p>
     * @param String $module <p>Имя модуля - владельца изображения</p>
     * @param Integer $moduleId <p>ID элемента - владельца изображения</p>
     * @param String $fileName <p>Имя загружаемого файла</p>
     * @param Boolean $isMain <p>Флаг главного изображения из ряда</p>
     * @return Boolean <p>результат операции</p>
     */
    function addImage ( $file, $module, $moduleId = 0, $fileName = '', $isMain = false )
    {
        if ( is_file ( $file ) )
        {
            //Альтернативный ключ (не по integer id)
            if ( !empty ( $moduleId ) && !is_numeric ( $moduleId ) )
            {
                if ( preg_match ( '%[\\\\/:*?<>|]+%', $moduleId ) )
                    return false;
                else
                {
                    $sql_select = "`alter_key`='" . $moduleId . "'";
                    $sql_update = "`alter_key`='" . $moduleId . "'";
                    $sql_insert = "`alter_key`";
                }
            }
            else
            {
                $sql_select = "`module_id`='" . $moduleId . "'";
                $sql_update = "`module_id`='" . $moduleId . "'";
                $sql_insert = "`module_id`";
            }

            $md5 = md5_file ( $file );
            if ( isset ( $this->alldata[$module][$moduleId][$md5] ) )
                return false;

            $ext = '';
            if ( !empty ( $fileName ) )
                $ext = strtolower ( pathinfo ( $fileName, PATHINFO_EXTENSION ) );

            //Проверка расширения
            if ( !in_array ( $ext, $this->allowedExts ) )
                return false;

            $dir = '/data/moduleImages/' . $module . '/' . $moduleId;
            $src = $dir . '/' . $md5 . '.' . $ext;
            $dest = DOCROOT . $src;

            if ( !is_dir ( DOCROOT . $dir ) )
                FileTools::createDir ( DOCROOT . $dir );

            FileTools::copyFile ( $file, $dest );
            $this->resize ( $dest, 500, 500 );
            $images = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='" . $module . "' AND " . $sql_select . " AND `main`='Y'" );
            if ( count ( $images ) == 0 )
                $isMain = true;

            if ( $isMain )
                SqlTools::execute ( "UPDATE `prefix_images` SET `main`='N'  WHERE `module`='" . $module . "' AND " . $sql_select . " AND `main`='Y'" );
            $lastId = SqlTools::insert ( "
                    INSERT INTO `prefix_images`
                            (`src`, `md5`, `module`, " . $sql_insert . ", `main`)
                    VALUES (
                            '" . $src . "',
                            '" . $md5 . "',
                            '" . $module . "',
                            '" . $moduleId . "',
                            '" . ( $isMain ? 'Y' : 'N' ) . "'
                    )
            " );
            return $lastId;
        }
        else
            return false;
    }

    /**
     * <pre>Помечает изображение как главное</pre>
     * @param Integer $id <p>Ид помечаемого изображения</p>
     * @return Boolean <p>Результат операции</p>
     */
    function starImage ( $id )
    {
        $id = ( int ) $id;
        $img = $this->getImage ( $id );
        if ( !$img )
            return false;

        SqlTools::execute ( "UPDATE `prefix_images` SET `main`='N' WHERE `module`='" . $img['module'] . "' AND ((`module_id`='" . $img['module_id'] . "' AND `module_id`!=0) OR (`alter_key`='" . $img['alter_key'] . "' AND `alter_key`!=''))" );
        SqlTools::execute ( "UPDATE `prefix_images` SET `main`='Y' WHERE `id`='" . $id . "'" );

        return true;
    }

    /**
     * <pre>Удаляет изображение из хранилища</pre>
     * @param Integer $id <p>Ид удаляемого изображения</p>
     * @return Boolean <p>Результат операции</p>
     */
    function delImage ( $id )
    {
        $id = ( int ) $id;
        $img = $this->getImage ( $id );
        if ( !$img )
            return false;

        SqlTools::execute ( "DELETE FROM `prefix_images` WHERE `id`='" . $id . "'" );
        if ( $img['main'] == 'Y' )
            SqlTools::execute ( "UPDATE `prefix_images` SET `main`='Y' WHERE `module`='" . $img['module'] . "' AND ((`module_id`='" . $img['module_id'] . "' AND `module_id`!=0) OR (`alter_key`='" . $img['alter_key'] . "' AND `alter_key`!='')) LIMIT 1" );

        return true;
    }

    /**
     * <pre>Добавляет ссылку на видео из Youtube к изображению</pre>
     * @param Integer $id <p>Ид изображения к которому добавляем видео</p>
     * @param String $url <p>Ссылка на видео из Youtube</p>
     * @return Boolean <p>Результат операции</p>
     */
    function addVideo ( $id, $url )
    {
        $id = ( int ) $id;
        $img = $this->getImage ( $id );
        if ( !$img )
            return false;

        SqlTools::execute ( "UPDATE `prefix_images` SET `video`='". $url ."' WHERE `id`='". $img[id] ."' AND `module_id`='" . $img[module_id] . "' AND `module`='" . $img[module] . "' ");
        return true;
    }

    /**
     * <pre>Возвращает строку данных (по полям в таблице) об изображении</pre>
     * @param Integer $id <p>Ид изображения</p>
     * @return Array <p>Строка данных</p>
     */
    public function getImage ( $id )
    {
        $id = ( int ) $id;
        return SqlTools::selectRow ( "SELECT * FROM `prefix_images` WHERE `id`='" . $id . "'" );
    }

    /**
     * <pre>Удаляет из хранилища все изображения привязанные к сущности</pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $module_id <p>Ид сущности</p>
     */
    function delImages ( $module, $module_id )
    {
        SqlTools::execute ( "DELETE FROM `prefix_images` WHERE `module`='" . $module . "' AND ((`module_id`='" . $module_id . "' AND `module_id`!=0) OR (`alter_key`='" . $module_id . "' AND `alter_key`!=''))" );
    }

    /**
     * <pre>Возвращает массив строк данных (по полям в таблице) о всех
     *  изображениях, привязанных к сущности</pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $module_id <p>Ид сущности</p>
     */
    public function getImages ( $module, $module_id )
    {
        return SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='$module' AND ((`module_id`='$module_id' AND `module_id`!=0) OR (`alter_key`='$module_id' AND `alter_key`!=''))", MYSQL_ASSOC );
    }

    /**
     * <pre>Подготавливает (собирает) строки данных о всех изображениях,
     * привязанных к сущностям модуля</pre>
     * @param String $module <p>Имя модуля (тип сущностей)</p>
     * @param Array $moduleIds <p>Массив ид сущностей для которых готовятся данные</p>
     * @param Boolean $onlyMain <p>Флаг, ограничивающий выборку только главными изображениями</p>
     * @return Boolean <p>Результат</p>
     */
    public function prepareImages ( $module, $moduleIds, $onlyMain = true )
    {
        if ( empty ( $moduleIds ) )
            return false;

        $clause = $onlyMain ? " AND `main`='Y'" : '';
        $images = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='$module' AND (`module_id` IN ("
                . ArrayTools::numberList ( $moduleIds )
                . ") OR `alter_key` IN ('"
                . ArrayTools::numberList ( $moduleIds )
                . "')) $clause", MYSQL_ASSOC );
        foreach ( $images as $image )
        {
            $this->preparedImgs[$module][$image['module_id']][] = $image;
        }
        foreach ( $moduleIds as $mid )
        {
            if ( !isset ( $this->preparedImgs[$module][$mid] ) )
                $this->preparedImgs[$module][$mid] = false;
        }
    }

    /**
     * <pre>
     * ВЫбирает для сущности из подготовленных изображений основное
     * или при отсутствии основного дефолтное изображение
     * </pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $moduleId <p>Id сущности</p>
     * @return Array
     */
    public function getMainImage ( $module, $moduleId )
    {
        if ( isset ( $this->preparedImgs[$module][$moduleId] ) )
        {
            if ( !$this->preparedImgs[$module][$moduleId] )
                $mainImage = null;
            else
                $mainImage = current ( $this->preparedImgs[$module][$moduleId] );
        }
        else
            $mainImage = SqlTools::selectRow ( "SELECT * FROM `prefix_images` WHERE `module`='" . $module . "' AND ((`module_id`='" . $moduleId . "' AND `module_id`!=0) OR (`alter_key`='" . $moduleId . "' AND `alter_key`!='')) AND `main`='Y' LIMIT 1", MYSQL_ASSOC );

        if ( !$mainImage || !file_exists ( DOCROOT . $mainImage['src'] ) )
            $mainImage = array ( 'src' => $this->defaultImage );

        return $mainImage;
    }

    /**
     * <pre>Возвращает массив главных изображений,
     * привязанных к сущностям модуля</pre>
     * @param String $module <p>Имя модуля (тип сущностей)</p>
     * @param Array $moduleIds <p>Массив ид сущностей для которых готовятся данные</p>
     * @return Boolean <p>Результат</p>
     */
    public function getMainImages ( $module, $moduleIds )
    {
        if ( empty ( $moduleIds ) )
            return false;

        $ids = ArrayTools::numberList ( $moduleIds );
        $aliases = ArrayTools::stringList ( $moduleIds );
        $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='$module' AND (`module_id` IN ($ids) OR `alter_key` IN ($aliases))  AND `main`='Y'", MYSQL_ASSOC );
        $images = [];
        foreach ( $rows as $row )
        {
            $images[$row['module_id']] = $row;
        }
        foreach ( $moduleIds as $mid )
        {
            if ( !isset ( $images[$mid] ) || !file_exists ( DOCROOT . $images[$mid]['src'] ) )
                $images[$mid] = [ 'src' => $this->defaultImage ];
        }
        return $images;
    }

    public function resize ( $src, $width, $height, $format = 'png', $bckg = null )
    {
        $fileDestination = '/data/thumbs' . $src . $width . 'x' . $height . '.' . $format;
        $pathToFile = DOCROOT . $fileDestination;
        if ( is_file ( $pathToFile ) )
            return $fileDestination;

        $fileSrc = DOCROOT . $src;
        if ( is_file ( $fileSrc ) )
        {
            $pathDestianation = pathinfo ( $pathToFile );
            if ( !is_dir ( $pathDestianation['dirname'] ) )
                mkdir ( $pathDestianation['dirname'], 0777, true );

            if ( $bckg )
                $command = "-resize " . $width . "x" . $height . " -size " . $width . "x" . $height . " xc:$bckg +swap -gravity center -quality 80 -composite $pathToFile";
            else
                $command = "-resize " . $width . "x" . $height . " $pathToFile";
            system ( "convert $fileSrc $command" );

            //Первый кадр анимированного гифа
            if ( !is_file ( $pathToFile ) )
            {
                $isAnim = substr ( $pathToFile, 0, -(strlen ( $format ) + 1) ) . '-0.' . $format;
                if ( is_file ( $isAnim ) )
                    rename ( $isAnim, $pathToFile );
                else
                    return image ( '/images/default.png', $width, $height );
            }

            return $fileDestination;
        }
        else
            return $this->resize ( $this->defaultImage, $width, $height, $format, $bckg );
    }
}