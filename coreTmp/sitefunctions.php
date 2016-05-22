<?php

/**
 * Строит модель содержимого для админ панели
 * @param type $table
 * @return type
 * @todo перенести в SiteContent
 */
function admGetContentTable ( $table = 'content' )
{
    $siteDocuments = SqlTools::selectRows ( "SELECT * FROM `prefix_$table` WHERE `deleted`='N' AND `show`='Y' ORDER BY `top`,`order`", MYSQL_ASSOC, "id" );

    $GLOBALS['siteDocsByModule'] = array ();
    foreach ( $siteDocuments as $doc )
    {
        //$docsBy
        $GLOBALS['siteDocsByParent'][$doc['top']][$doc['id']] = $doc;
        if ( isset ( $doc['module'] ) )
        {
            if ( !isset ( $GLOBALS['siteDocsByModule'][$doc['module']] ) )
                $GLOBALS['siteDocsByModule'][$doc['module']] = $doc;
        }
    }

    return array
    (
        $siteDocuments,
        Starter::app ()->content->docsByParent,
        Starter::app ()->content->docsByModule
    );
}

function admLinkById ( $id, $table = 'content' )
{
    list($siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule) = admGetContentTable ( $table );

    $admLinkById_ = function($id, $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_)
    {
        if ( $id == 0 )
            return;

        $link = $admLinkById_ ( $siteDocuments[$id]['top'], $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_ ) . '/' . $siteDocuments[$id]['nav'];
        return $link;
    };

    return $admLinkById_ ( $id, $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_ );
}

function admLinkByModule ( $module, $table = 'content' )
{
    list($siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule) = admGetContentTable ( $table );

    return admLinkById ( $siteDocumentsByModule[$module]['id'], $table );
}

$modulesSettings = array ();

function admGetSet ( $module, $callname, $default = '' )
{
    global $modulesSettings;
    if ( empty ( $modulesSettings ) )
    {
        $sets = SqlTools::selectRows ( "SELECT * FROM `prefix_settings`", MYSQL_ASSOC );
        foreach ( $sets as $k => $v )
            $modulesSettings[$v['module']][$v['callname']] = $v;
    }

    if ( isset ( $modulesSettings[$module][$callname] ) )
        return $modulesSettings[$module][$callname]['value'];
    else
        return $default;
}
