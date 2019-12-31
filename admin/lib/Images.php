<?php

/**
 * <pre>Класс для загрузки, удаления и других операций с изображениями</pre>
 *
 */
class Images
{
    private $alldata;
    private $allowed_exts = array ( 'gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff' );

    /**
     * <pre>Добавляет изображение в хранилище</pre>
     * @param File $file <p>Загружаемый файл</p>
     * @param String $module <p>Имя модуля - владельца изображения</p>
     * @param Integer $module_id <p>Ид элемента - владельца изображения</p>
     * @param String $fileName <p>Имя загружаемого файла</p>
     * @param Boolean $isMain <p>Флаг главного изображения из ряда</p>
     * @return Boolean <p>результат операции</p>
     */
    function AddImage ( $file, $module, $module_id = 0, $fileName = '', $isMain = false )
    {
        if ( is_file ( $file ) )
        {
            //Альтернативный ключ (не по integer id)
            if ( !empty ( $module_id ) && !is_numeric ( $module_id ) )
            {
                if ( preg_match ( '%[\\\\/:*?<>|]+%', $module_id ) )
                    return false;
                else
                {
                    $sql_select = "`alter_key`='" . $module_id . "'";
                    $sql_update = "`alter_key`='" . $module_id . "'";
                    $sql_insert = "`alter_key`";
                }
            }
            else
            {
                $sql_select = "`module_id`='" . $module_id . "'";
                $sql_update = "`module_id`='" . $module_id . "'";
                $sql_insert = "`module_id`";
            }

            $md5 = md5_file ( $file );
            if ( isset ( $this->alldata[$module][$module_id][$md5] ) )
                return false;

            $ext = '';
            if ( !empty ( $fileName ) )
            {
                $ext = strtolower ( pathinfo($fileName, PATHINFO_EXTENSION));
            }

            //Проверка расширения
            if ( !in_array ( $ext, $this->allowed_exts ) )
            {
                return false;
            }

            $dir = '/data/moduleImages/' . $module . '/' . $module_id;
            $src = $dir . '/' . $md5 . '.' . $ext;
            $dest = DOCROOT . $src;

            if ( !is_dir ( DOCROOT . $dir ) )
            {
                mkdir ( DOCROOT . $dir, 0777, true );
            }
            copy ( $file, $dest );
            $this->resize ( $dest, 500, 500 );

            $images = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='" . $module . "' AND " . $sql_select . " AND `main`='Y'" );
            if ( count ( $images ) == 0 )
            {
                $isMain = true;
            }

            if ( $isMain )
            {
                SqlTools::execute ( "UPDATE `prefix_images` SET `main`='N'  WHERE `module`='" . $module . "' AND " . $sql_select . " AND `main`='Y'" );
            }
            $lastId = SqlTools::insert ( "
                    INSERT INTO `prefix_images`
                            (`src`, `md5`, `module`, " . $sql_insert . ", `main`)
                    VALUES (
                            '" . $src . "',
                            '" . $md5 . "',
                            '" . $module . "',
                            '" . $module_id . "',
                            '" . ( $isMain ? 'Y' : 'N' ) . "'
                    )
            " );
            return $lastId;
        }
        else
        {
            return false;
        }
    }

    /**
     * <pre>Помечает изображение как главное</pre>
     * @param Integer $id <p>Ид помечаемого изображения</p>
     * @return Boolean <p>Результат операции</p>
     */
    function StarImage ( $id )
    {
        $id = ( int ) $id;
        $img = $this->GetImage ( $id );
        if ( !$img )
        {
            return false;
        }
        SqlTools::execute ( "UPDATE `prefix_images` SET `main`='N' WHERE `module`='" . $img['module'] . "' AND ((`module_id`='" . $img['module_id'] . "' AND `module_id`!=0) OR (`alter_key`='" . $img['alter_key'] . "' AND `alter_key`!=''))" );
        SqlTools::execute ( "UPDATE `prefix_images` SET `main`='Y' WHERE `id`='" . $id . "'" );

        return true;
    }

    /**
     * <pre>Удаляет изображение из хранилища</pre>
     * @param Integer $id <p>Ид удаляемого изображения</p>
     * @return Boolean <p>Результат операции</p>
     */
    function DelImage ( $id )
    {
        $id = ( int ) $id;
        $img = $this->GetImage ( $id );
        if ( !$img )
        {
            return false;
        }
        SqlTools::execute ( "DELETE FROM `prefix_images` WHERE `id`='" . $id . "'" );
        if ( $img['main'] == 'Y' )
        {
            SqlTools::execute ( "UPDATE `prefix_images` SET `main`='Y' WHERE `module`='" . $img['module'] . "' AND ((`module_id`='" . $img['module_id'] . "' AND `module_id`!=0) OR (`alter_key`='" . $img['alter_key'] . "' AND `alter_key`!='')) LIMIT 1" );
        }
        return true;
    }

    /**
     * <pre>Добавляет ссылку на видео из Youtube и описание к изображению</pre>
     * @param Integer $id <p>Ид изображения к которому добавляем видео</p>
     * @param String $url <p>Ссылка на видео из Youtube</p>
     * @return Boolean <p>Результат операции</p>
     */
    function AddVideo ( $id, $url, $title )
    {
        $id = ( int ) $id;
        $img = $this->GetImage ( $id );
        if ( !$img )
        {
            return false;
        }
        SqlTools::execute ( "UPDATE `prefix_images` SET `video`='". $url ."', `title`='". $title ."' WHERE `id`='". $img[id] ."' AND `module_id`='" . $img[module_id] . "' AND `module`='" . $img[module] . "' ");
        return true;
    }

    /**
     * <pre>Удаляет из хранилища все изображения привязанные к сущности</pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $module_id <p>Ид сущности</p>
     */
    function DelImages ( $module, $module_id )
    {
        SqlTools::execute ( "DELETE FROM `prefix_images` WHERE `module`='" . $module . "' AND ((`module_id`='" . $module_id . "' AND `module_id`!=0) OR (`alter_key`='" . $module_id . "' AND `alter_key`!=''))" );
    }

    /**
     * <pre>Возвращает строку данных (по полям в таблице) об изображении</pre>
     * @param Integer $id <p>Ид изображения</p>
     * @return Array <p>Строка данных</p>
     */
    function GetImage ( $id )
    {
        $id = ( int ) $id;
        return SqlTools::selectRow ( "SELECT * FROM `prefix_images` WHERE `id`='" . $id . "'" );
    }

    /**
     * <pre>Возвращает массив строк данных (по полям в таблице) о всех
     *  изображениях, привязанных к сущности</pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $module_id <p>Ид сущности</p>
     */
    function GetImages ( $module, $module_id )
    {
        return SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='$module' AND ((`module_id`='$module_id' AND `module_id`!=0) OR (`alter_key`='$module_id' AND `alter_key`!=''))", MYSQL_ASSOC );
    }

    /**
     * <pre>
     * ВЫбирает для сущности из подготовленных изображений основное
     * или при отсутствии основного дефолтное изображение
     * </pre>
     * @param String $module <p>Имя модуля (тип сущности)</p>
     * @param Integer $module_id <p>Id сущности</p>
     * @return Array
     */
    function GetMainImage ( $module, $module_id )
    {
        $defaultImage = file_exists ( TEMPL . DS . Starter::app ()->getTheme () . DS . 'images' . DS . 'nophoto.png' ) ? DS . 'themes' . DS . Starter::app ()->getTheme () . DS . 'images' . DS . 'nophoto.png' : DS . 'images' . DS . 'default.png';
        if ( isset ( $this->preparedImgs[$module][$module_id] ) )
        {
            if ( !$this->preparedImgs[$module][$module_id] )
            {
                $mainImage = null;
            }
            else
            {
                $mainImage = current ( $this->preparedImgs[$module][$module_id] );
            }
        }
        else
        {
            $mainImage = SqlTools::selectRow ( "SELECT * FROM `prefix_images` WHERE `module`='" . $module . "' AND ((`module_id`='" . $module_id . "' AND `module_id`!=0) OR (`alter_key`='" . $module_id . "' AND `alter_key`!='')) AND `main`='Y' LIMIT 1", MYSQL_ASSOC );
        }

        if ( !$mainImage || !file_exists ( DOCROOT . $mainImage['src'] ) )
        {
            $mainImage = array ( 'src' => $defaultImage );
        }

        return $mainImage;
    }

    /**
     * <pre>Подготавливает (собирает) строки данных о всех изображениях,
     * привязанных к сущностям модуля</pre>
     * @param String $module <p>Имя модуля (тип сущностей)</p>
     * @param Array $moduleIds <p>Массив ид сущностей для которых готовятся данные</p>
     * @param Boolean $onlyMain <p>Флаг, ограничивающий выборку только главными изображениями</p>
     * @return Boolean <p>Результат</p>
     */
    function PrepareImages ( $module, $moduleIds, $onlyMain = true )
    {
        if ( empty ( $moduleIds ) )
        {
            return false;
        }
        $clause = $onlyMain ? " AND `main`='Y'" : '';
        $images = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module`='" . $module . "' AND (`module_id` IN ("
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
            {
                $this->preparedImgs[$module][$mid] = false;
            }
        }
    }

    /**
     * Изменяет размеры изображения
     * @param string $src_image путь к файлу
     * @param integer $dest_width новая ширина изображения
     * @param integer $dest_height новая высота изображения
     * @param integer $quality качество изображения
     * @param boolean $max изменять по максимальной стороне
     * @return boolean
     */
    public static function resize ( $src_image, $dest_width, $dest_height, $quality = 80, $max = false )
    {
        $src_image = DOCROOT . $src_image;

        if ( !$quality )
        {
            $quality = 80;
        }
        if ( !$src_image )
        {
            return false;
        }

        $dest_image = dirname ( $src_image ) . DS . 'small_' . basename ( $src_image );

        $info = getImageSize ( $src_image );
        switch ( $info[2] )
        {
            case 1:
                $src_img = imageCreateFromGIF ( $src_image );
                break;
            case 2:
                $src_img = imageCreateFromJPEG ( $src_image );
                break;
            case 3:
                $src_img = imageCreateFromPNG ( $src_image );
                break;
            default:
                return false;
        }

        $src_width = imageSX ( $src_img );
        $src_height = imageSY ( $src_img );

        if ( $dest_width > $src_width && $dest_height > $src_height )
        {
            return false;
        }


        $mc1 = $dest_width / $src_width;
        $mc2 = $dest_height / $src_height;

        if ( $max )
        {
            $k = max ( $mc1, $mc2 );
        }
        else
        {
            $k = min ( $mc1, $mc2 );
        }

        $dest_width = round ( $src_width * $k );
        $dest_height = round ( $src_height * $k );
        if ( $max && ($dest_width > $src_width || $dest_height > $src_height) )
        {
            $k = min ( $mc1, $mc2 );
            $dest_width = round ( $src_width * $k );
            $dest_height = round ( $src_height * $k );
        }
        $dst_img = imageCreateTrueColor ( $dest_width, $dest_height );

        //png
        imagecolortransparent ( $dst_img, imagecolorallocatealpha ( $dst_img, 0, 0, 0, 127 ) );
        imagealphablending ( $dst_img, false );
        imagesavealpha ( $dst_img, true );

        imageCopyResampled ( $dst_img, $src_img, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height );

        switch ( $info[2] )
        {
            case 1:
                imageGIF ( $dst_img, $dest_image );
                break;
            case 2:
                imageJPEG ( $dst_img, $dest_image, $quality );
                break;
            case 3:
                imagePNG ( $dst_img, $dest_image );
                break;
            default:
                return false;
        }
        imageDestroy ( $src_img );
        imageDestroy ( $dst_img );

        return true;
    }

    public function ProccessImages ( $module, $module_id = null )
    {
        $where = '';
        if ( $module )
        {
            $where .= " `module`='" . $module . "'";
        }
        if ( $module_id && $module )
        {
            $where .= " AND ((`module_id`='" . $module_id . "' AND `module_id`!=0) OR (`alter_key`='" . $module_id . "' AND `alter_key`!=''))";
        }

        $images = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE " . $where, MYSQL_ASSOC );

        if ( $_GET['action'] == 'resize' )
        {
            $width = Tools::getSettings ( $module, 'small_image_width', 500 );
            $height = Tools::getSettings ( $module, 'small_image_height', 500 );
            $quality = Tools::getSettings ( $module, 'small_image_quality', 80 );
            $max = Tools::getSettings ( $module, 'small_image_max', false );

            foreach ( $images as $image )
            {
                $this->resize ( $image['src'], $width, $height, $quality, $max );
            }

            $text = 'Все фото (' . count ( $images ) . ' шт.) обработаны';
        }
        if ( $_GET['action'] == 'delete' )
        {
            $k = 0;
            foreach ( $images as $image )
            {
                $used[$image['module_id']][] = basename ( $image['src'] );
                $used[$image['module_id']][] = 'small_' . basename ( $image['src'] );
            }
            foreach ( $used as $id => $image )
            {
                $files = scandir ( DOCROOT . DS . 'data' . DS . 'moduleImages' . DS . $module . DS . $id );

                foreach ( $files as $file )
                {
                    if ( !in_array ( $file, $used[$id] ) )
                    {
                        unlink ( DOCROOT . DS . 'data' . DS . 'moduleImages' . DS . $module . DS . $id . DS . $file );
                        $k++;
                    }
                }
            }
            $text = 'Неиспользуемые фото (' . $k . ' шт.) удалены';
        }

        return $text;
    }
}
