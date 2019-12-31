<?php

if ( isset ( $_POST['lib_comments'] ) )
{
    if ( !session_id () )
    {
        session_start ();
    }

    ob_start ();
    $action = $_POST['action'];
    if ( $action == 'add' )
    {
        $r = comments_add ();
    }
    else if ( $action == 'del' )
    {
        $r = comments_del ();
    }
    else if ( $action == 'mass_prune' )
    {
        $r = comments_mass_prune ();
    }
    ob_end_clean ();

    echo $r;
}

function comments_add ()
{
    $parent_id = ( int ) $_POST['parent_id'];
    $author = $_POST['author'];
    $email = $_POST['email'];

    $text = $_POST['text'];

    $module = $_POST['module'];
    $element_id = $_POST['element_id'];


    if ( !$author )
    {
        return '{"error":"empty_author"}';
    }
    if ( !$email )
    {
        return '{"error":"empty_email"}';
    }
    if ( !$text )
    {
        return '{"error":"empty_text"}';
    }
    setcookie ( 'cmt_name', $_POST['author'], time () + 86400 * 365 );
    setcookie ( 'cmt_email', $_POST['email'], time () + 86400 * 365 );
    $_COOKIE['cmt_name'] = $_POST['author'];
    $_COOKIE['cmt_email'] = $_POST['email'];

    $sql = "INSERT INTO `prefix_comments`
            (`parent_id`, `author`, `email`, `text`, `module`, `element_id`)
            VALUES ({$parent_id}, '{$author}', '{$email}', '{$text}', '{$module}', '{$element_id}')";
    SqlTools::insert ( $sql );
}

function comments_del ()
{
    $is_admin = ($_SESSION['admin']['type'] == 'a');
    if ( !$is_admin )
    {
        return '{"error":"not_authorized"}';
    }
    $id = ( int ) $_POST['comment_id'];
    if ( !$id )
    {
        return '{"error":"no_comment_id"}';
    }
    $sql = "UPDATE `prefix_comments` SET `deleted`='Y' WHERE `id`='" . $id . "'";
    SqlTools::execute ( $sql );

    return comments_block ( $_POST['hash'], true );
}

function comments_mass_prune ()
{
    $is_admin = ($_SESSION['admin']['type'] == 'a');
    if ( !$is_admin )
    {
        return '{"error":"not_authorized"}';
    }
    $ids = $_POST['comment_ids'];
    foreach ( $ids as $k => $v )
    {
        if ( !$v )
        {
            unset ( $ids[$k] );
        }
        if ( $v != ( int ) $v )
        {
            unset ( $ids[$k] );
        }
        $ids[$k] = ( int ) $v;
    }
    if ( !count ( $ids ) )
    {
        return '{"error":"no_comment_ids"}';
    }
    $ids_str = implode ( ',', $ids );
    $sql = "UPDATE `prefix_comments` SET `deleted`='Y' WHERE `id` IN (" . $ids_str . ")";
    SqlTools::execute ( $sql );

    return comments_block ( $_POST['hash'], true );
}

function comments_block ( $module, $element_id )
{
    $sql = "SELECT * FROM `prefix_comments`
            WHERE `module`='" . $module . "' AND `element_id`='" . $element_id . "'
            ORDER BY `timestamp` ASC";
    $rows = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );
    $comments = array ();
    foreach ( $rows as $r )
    {
        $comments[$r['parent_id']][$r['id']] = $r;
    }

    return tpl ( 'parts/comments', array (
        'comments' => $comments,
        'module' => $module,
        'element_id' => $element_id
    ) );
}

function comments_hash ( $hash_input )
{
    return md5 ( $hash_input );
}

function comments_ts_printable ( $ts )
{
    if ( !is_int ( $ts ) )
    {
        $ts = strtotime ( $ts );
    }
    $months = array ( 1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );

    return date ( 'd', $ts ) . ' ' . $months[( int ) date ( 'm', $ts )] . ' ' . date ( 'Y', $ts ) . ', ' . date ( 'H:i', $ts );
}

function comments_count ( $module, $element_id )
{
    $sql = "SELECT DISTINCT(`id`) FROM `prefix_comments` WHERE `module`='" . $module . "' AND `element_id`='" . $element_id . "'";

    return SqlTools::selectRow ( $sql );
}

function last_comments ( $module, $element_id = null )
{
    $where = '';
    if ( $element_id )
    {
        $where .= " AND `element_id`='" . $element_id . "'";
    }

    $sql = "SELECT * FROM `prefix_comments`
            WHERE `module`='" . $module . "'" . $where
        . " ORDER BY `timestamp` DESC"
        . " LIMIT 5";

    $comments = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

    $class = new $module;

    foreach ( $comments as &$r )
    {
        $r['title'] = SqlTools::selectValue ( "SELECT `name` FROM `prefix_" . $module . "` WHERE `id`='" . $r['element_id'] . "'", MYSQLI_ASSOC );
        $r['link'] = $class->Link ( array (), $r['element_id'] );
    }

    return tpl ( 'parts/last_comments', array (
        'comments' => $comments
    ) );
}
