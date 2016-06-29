<?php

/**
 * Берем функции из админки (тоже очень полезные)
 */
require_once DOCROOT . '/admin/lib/sysfunctions.php';

/**
 * Кэш блоков на время выполнения
 */
$blocksByCallname = $blocksById = array ();

/**
 * Возвращает содержимое блока
 *
 * @param int $id ID блока
 * @param bool $return Вернуть вызвавшему, иначе echo
 */
function block ( $id, $return = false )
{
    if ( $id === '0' )
        return;

    global $blocksByCallname, $blocksById;
    if ( empty ( $blocksByCallname ) )
    {
        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (b.`id` = m2r.`module_id` AND m2r.`module` = 'Blocks')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }
        $sql = "SELECT b.*" . $select
            . " FROM `prefix_blocks` AS b"
            . $join
            . " WHERE b.`deleted` = 'N' AND b.`show` = 'Y'" . $where;

        $blocks = SqlTools::selectRows ( $sql, MYSQL_ASSOC );
        foreach ( $blocks as $b )
        {
            $blocksByCallname[$b['callname']] = $b;
            $blocksById[$b['id']] = $b;
        }
    }

    if ( is_numeric ( $id ) )
    {
        if ( isset ( $blocksById[$id] ) )
            $block = $blocksById[$id];
        else
            echo '';//error ( 'Не нашел блок ' . $id );
    }
    else
    {
        if ( isset ( $blocksByCallname[$id] ) )
            $block = $blocksByCallname[$id];
        else
            return;
    }

    //Если админ
    if ( !session_id () )
        session_start ();

    $godmode_suspended = Tools::getSettings ( 'Blocks', 'godmode_suspended', true );
    $bool = ($godmode_suspended == 'true');
//    if ( isset ( $_SESSION['admin']['type'] ) && $bool )
//    {
//        $is_admin = ($_SESSION['admin']['type'] == 'a');
//        if ( $is_admin && @!$_SESSION['godmode_suspended'] )
//        {
//            // Если html содержимое
//            if ( mb_strlen ( strip_tags ( $block['text'] ) ) != mb_strlen ( $block['text'] ) )
//                $edit_block_link = '/admin/?module=Blocks&method=Info&edit_text=' . $block['id'];
//            else
//                $edit_block_link = '/admin/?module=Blocks#open' . $block['id'];
//            $block['text'] = '<div style="border:dashed 1px grey; position:relative;">' . $block['text'] . '<a target="_blank" style="position:absolute; top:0; right:-8px; z-index:100;" href="' . $edit_block_link . '" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать" /></a></div>';
//        }
//    }

    if ( $block['show'] == 'Y' )
    {
        if ( $return )
            return $block['text'];
        else
            echo $block['text'];
    }
    else
        return false;
}

function getBlock ( $id )
{
    if ( is_numeric ( $id) )
        $whereClause = "`id`=$id";
    else
        $whereClause = "`callname`='$id'";

    $sql = "SELECT * FROM `prefix_blocks`
        WHERE `deleted`='N' AND `show`='Y' AND " . $whereClause;

    $block = SqlTools::selectObject ( $sql );
    return $block;
}


/**
 * Вывод ошибки
 * @param string $error
 */
function error ( $error )
{
    if ( !Starter::app ()->develop )
    {
        return false;
    }
    $array_debug = debug_backtrace ();
    if ( !empty ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
    {
        echo $array_debug[0]['file'] . ': ' . $array_debug[0]['line'] . "\r\n" . $error;
    }
    else
    {
        echo '<div style="background:white; color:black display:block;font-family:Trebuchet MS,Arial,sans-serif;">
            <h1 style="color:#0498EC;font-size:1.5em;">Ошибка</h1>
                <ul style="padding: 0em 1em 1em 1.55em;font-size:0.8em;margin:0 0 0 2.5em;">
                    <li>Файл <code style="font-weight:bold;">' . $array_debug[0]['file'] . '</code> на строке: <code style="font-weight:bold;">' . $array_debug[0]['line'] . '</code> говорит что:</li>
                    <li>' . $error . '</li>
                </ul>
            </div>';
    }
}
