<?php

function image ( $src, $width, $height, $format = 'png', $bckg = null )
{
    $new_file_src = '/data/thumbs' . $src . $width . 'x' . $height . '.' . $format;
    $new_file = DOCROOT . $new_file_src;
    if ( is_file ( $new_file ) )
    {
        return $new_file_src;
    }
    $file = DOCROOT . $src;
    if ( is_file ( $file ) )
    {
        $new_file_path_parts = pathinfo ( $new_file );
        if ( !is_dir ( $new_file_path_parts['dirname'] ) )
        {
            mkdir ( $new_file_path_parts['dirname'], 0777, true );
        }

        if ( $bckg )
        {
            $command = "-resize " . $width . "x" . $height . " -size " . $width . "x" . $height . " xc:$bckg +swap -gravity center  -composite $new_file";
        }
        else
        {
            $command = "-resize " . $width . "x" . $height . " $new_file";
        }
        system ( "convert $file $command" );

        //Первый кадр анимированного гифа
        if ( !is_file ( $new_file ) )
        {
            $check_anim = substr ( $new_file, 0, -(strlen ( $format ) + 1) ) . '-0.' . $format;
            if ( is_file ( $check_anim ) )
            {
                rename ( $check_anim, $new_file );
            }
            else
            {
                return image ( '/images/default.png', $width, $height );
            }
        }

        return $new_file_src;
    }
    else
    {
        return image ( NOT_FOUND_IMAGE_FILE, $width, $height );
    }
}

function imageForStorage ( $src, $width, $height, $format = 'png' )
{
    $fileName = basename ( $src );
    $new_file_src = DS . CACHE . DS . $fileName . $width . 'x' . $height . '.' . $format;
    $new_file = DOCROOT . $new_file_src;

    if ( is_file ( $new_file ) )
    {
        return $new_file_src;
    }

    $file = DOCROOT . $src;
    if ( is_file ( $file ) )
    {
        $new_file_path_parts = pathinfo ( $new_file );
        if ( !is_dir ( $new_file_path_parts['dirname'] ) )
        {
            mkdir ( $new_file_path_parts['dirname'], 0777, true );
        }

        system ( 'convert "' . $file . '" -resize ' . $width . 'x' . $height . ' "' . $new_file . '"' );

        //Первый кадр анимированного гифа
        if ( !is_file ( $new_file ) )
        {
            $check_anim = substr ( $new_file, 0, -(strlen ( $format ) + 1) ) . '-0.' . $format;
            if ( is_file ( $check_anim ) )
            {
                rename ( $check_anim, $new_file );
            }
            else
            {
                return imageForStorage ( '/images/default.png', $width, $height );
            }
        }

        return $new_file_src;
    }
    else
    {
        return imageForStorage ( '/images/default.png', $width, $height );
    }
}

function imageLandscape ( $src )
{
    list($width, $height) = getimagesize ( DOCROOT . $src );
    if ( $width > 0 && $height > 0 )
    {
        if ( $width > $height )
        {
            return true;
        }
    }
    return false;
}

function img ()
{
    require_once DOCROOT . '/admin/lib/Images.php';
    return ComponentFactory::getComponent ( 'Images' );
}

function files ()
{
    require_once DOCROOT . '/admin/lib/MediaFiles.php';
    return ComponentFactory::getComponent ( 'MediaFiles' );
}
