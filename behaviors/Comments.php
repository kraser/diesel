<?php
/**
 * Description of Comments
 *
 * @author kraser
 */
class Comments extends CmsBehavior
{
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
    }

    public function getComments ( $module, $moduleId )
    {
        $query = "SELECT
                `id` AS id,
                `parent_id` AS parentId,
                `hash` AS hash,
                `author` AS author,
                `email` AS email,
                `text` AS text,
                `module` AS module,
                `element_id` AS elementId,
                `timestamp` AS timestamp
            FROM `prefix_comments`
            WHERE `module`='$module' AND `element_id`=$moduleId
            ORDER BY `timestamp` ASC";
        $rows = SqlTools::selectObjects ( $query, null, "id" );

        $comments = [];
        foreach ( $rows as $id => $row )
        {
            if ( $row->parentId == 0 )
                $comments[] = $row;
            else
                $rows[$row->parentId]->comments[] = $row;
        }

        return $comments;
    }

    function putComment ()
    {
        $parentId = ( int ) Starter::app ()->urlmanager->getParameter ( 'parentId' );
        $author = SqlTools::escapeString ( Starter::app ()->urlmanager->getParameter ( 'author' ) );
        $email = SqlTools::escapeString ( Starter::app ()->urlmanager->getParameter ( 'email' ) );
        $text = SqlTools::escapeString ( Starter::app ()->urlmanager->getParameter ( 'text' ) );
        $module = SqlTools::escapeString ( Starter::app ()->urlmanager->getParameter ( 'module' ) );
        $elementId = (int) Starter::app ()->urlmanager->getParameter ( 'elementId' );


//        if ( !$author )
//        {
//            return '{"error":"empty_author"}';
//        }
//        if ( !$email )
//        {
//            return '{"error":"empty_email"}';
//        }
//        if ( !$text )
//        {
//            return '{"error":"empty_text"}';
//        }
//        setcookie ( 'cmt_name', $_POST['author'], time () + 86400 * 365 );
//        setcookie ( 'cmt_email', $_POST['email'], time () + 86400 * 365 );
//        $_COOKIE['cmt_name'] = $_POST['author'];
//        $_COOKIE['cmt_email'] = $_POST['email'];

        $query = "INSERT INTO `prefix_comments`
            (`parent_id`, `author`, `email`, `text`, `module`, `element_id`)
            VALUES ($parentId, '$author', '$email', '$text', '$module', $elementId)";
        $id = SqlTools::insert ( $query );
        return SqlTools::selectObject("SELECT
                `id` AS id,
                `parent_id` AS parentId,
                `hash` AS hash,
                `author` AS author,
                `email` AS email,
                `text` AS text,
                `module` AS module,
                `element_id` AS elementId,
                `timestamp` AS timestamp
            FROM `prefix_comments`
            WHERE `id`=$id" );
    }
}
