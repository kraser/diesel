<?php

function getImageStorage ( $path )
{
    // ид картинки / считаем, что имя картинки с путём - уникально в таблице и его достаточно для извлечения
    $imageId = SqlTools::selectValue ( "SELECT `id` FROM `prefix_images_storage` WHERE `path`='" . $path . "' AND `del`='N'" );
    if ( $imageId == null )
    {  // нет такой картинки
        return false;
    }

    return $imageId;
}

function getModuleTable ( $module )
{
    // какая таблица у модуля
    $tbl = $module::$table;

    return $tbl;
}

function getModuleRowImagesIds ( $module, $module_id )
{
    // какая таблица у модуля
    $tbl = getModuleTable ( $module );
    // ид изображений данной сущности данного модуля, первый ид - "главной" картинки
    $ids = SqlTools::selectValue ( "SELECT `images_ids` FROM `prefix_" . $tbl . "` WHERE `id`='" . $module_id . "'" );
    $ids = trim ( $ids );
    if ( empty ( $ids ) || $ids === null )
    { // нет правильного ответа
        return false;
    }
    $ids = explode ( ',', $ids );

    return $ids;
}

function setModuleRowImagesIds ( $module, $module_id, $imgIds = array () )
{
    $imgIds = implode ( ',', $imgIds );
    // какая таблица у модуля
    $tbl = getModuleTable ( $module );
    // ид изображений данной сущности данного модуля, первый ид - "главной" картинки
    $result = SqlTools::execute ( "UPDATE `prefix_" . $tbl . "` SET `images_ids`='" . $imgIds . "' WHERE `id`='" . $module_id . "'" );

    return $result;
}

function addImageStorage ( $file, $module, $module_id = 0, $file_name = '', $is_main = false )
{
    if ( is_file ( $file ) )
    {
        // ид картинки / считаем, что имя картинки с путём - уникально в таблице и его достаточно для извлечения
        $image_id = getImageStorage ( str_replace ( DOCROOT, '', $file ) );
        if ( !$image_id )
        {
            return false;
        }
        // ид изображений данной сущности данного модуля, первый ид - "главной" картинки
        $imgIds = getModuleRowImagesIds ( $module, $module_id );
        if ( in_array ( $image_id, $imgIds ) )
        { // не дадим добавить уже добавленное
            return false;
        }
        // добавляем, с учётом, что первый ид - "главной" картинки
        $mainId = '';
        if ( $imgIds )
        {
            $mainId = array_shift ( $imgIds );
        }
        $imgIds[] = $image_id;
        if ( !empty ( $mainId ) )
        {
            sort ( $imgIds );
            array_unshift ( $imgIds, $mainId );
        }
        $result = setModuleRowImagesIds ( $module, $module_id, $imgIds );
    }
    else
    {
        return false;
    }
}

function setMainImageStorage ( $module, $module_id, $mainImgId )
{
    $imgIds = getModuleRowImagesIds ( $module, $module_id );
    if ( !$imgIds )
    {
        return false;
    }
    if ( in_array ( $mainImgId, $imgIds ) )
    {
        // удаляем существующий ИД из массива
        $arr = array ();
        foreach ( $imgIds as $imgId )
        {
            if ( $imgId != $mainImgId )
            {
                $arr[] = $imgId;
            }
        }
        $imgIds = $arr;
    }
    // добавляем ИД в начало массива
    sort ( $imgIds );
    array_unshift ( $imgIds, $mainImgId );
    $result = setModuleRowImagesIds ( $module, $module_id, $imgIds );

    return $result;
}

function delImageStorage ( $module, $module_id, $delImgId )
{
    $imgIds = getModuleRowImagesIds ( $module, $module_id );
    if ( !$imgIds )
    {
        return false;
    }
    if ( !in_array ( $delImgId, $imgIds ) )
    {
        return false;
    }
    // удаляем существующий ИД из массива
    $arr = array ();
    foreach ( $imgIds as $imgId )
    {
        if ( $imgId != $delImgId )
        {
            $arr[] = $imgId;
        }
    }
    $imgIds = $arr;
    // первый ид - "главной" картинки - сохранится
    $result = setModuleRowImagesIds ( $module, $module_id, $imgIds );

    return $result;
}

function getImagesStorageByIds ( $imgIds = array () )
{
    if ( !$imgIds )
    {
        return false;
    }
    $strIds = implode ( ',', $imgIds );
    $imgs = SqlTools::selectRows ( "SELECT * FROM `prefix_images_storage` WHERE `id` IN (" . $strIds . ") AND `del`='N'" );

    return $imgs;
}

function getImagesStorageByModuleId ( $module, $module_id, $onlyMain = false )
{
    $imgIds = getModuleRowImagesIds ( $module, $module_id );
    if ( !$imgIds )
    {
        return false;
    }
    if ( $onlyMain )
    { // если нужно только главное изображение
        $imgIds = array ( $imgIds[0] );
    }
    $imgs = getImagesStorageByIds ( $imgIds );
    if ( !$imgs )
    {
        return false;
    }
    // ставим признак главности/неглавности
    // первый ид - "главной" картинки
    $mainId = $imgIds[0];
    $result = array ();
    foreach ( $imgs as $img )
    {
        if ( $img['id'] == $mainId )
        {
            $img['main'] = 'Y';
            array_unshift ( $result, $img );
            continue;
        }
        $img['main'] = 'N';
        $result[] = $img;
    }

    return $result;
}
