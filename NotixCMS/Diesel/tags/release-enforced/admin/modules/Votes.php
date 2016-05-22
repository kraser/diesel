<?php

class Votes extends AdminModule
{
    const name = 'Опросы';
    const order = 21;
    const icon = 'signal';
    public $submenu = array
        (
        'Info'        => 'Опросы',
        'Userversion' => 'Варианты пользователей',
        'Settings'    => 'Настройки модуля',
        'Trash'       => 'Корзина'
    );
    private $table = 'votes';
    private $table_answers = 'votes_answers';
    private $table_userversion = 'votes_userversion';

    public function Info ()
    {
        $sql = "SELECT * FROM `prefix_" . $this->table . "` WHERE `deleted`='N' ORDER BY `order`, `id`";
        $votes = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

        $this->title = self::name;
        $this->content = tpl ( 'modules/' . __CLASS__ . '/list', array (
            'link'   => $this->GetLink ( 'Info' ),
            'addnew' => $this->GetLink ( 'Addnew' ),
            'edit'   => $this->GetLink ( 'Edit' ),
            'delete' => $this->GetLink ( 'Delete' ),
            'module' => __CLASS__,
            'votes'  => $votes
        ) );
    }

    public function Addnew ()
    {
        $this->title = 'Добавить новый - ' . self::name;
        $this->content = tpl ( 'modules/' . __CLASS__ . '/addnew', array (
            'link'     => $this->GetLink ( 'Info' ),
            'action'   => $this->GetLink ( 'Save' ),
            'validate' => $this->GetLink ( 'Validate' ),
            'module'   => __CLASS__,
        ) );
    }

    public function Edit ()
    {
        $id = (!empty ( $_GET['id'] )) ? $_GET['id'] : null;
        if ( !$id )
        {
            header ( "Location: " . $this->GetLink ( 'Info' ) );
            die ();
        }

        $sql = "SELECT * FROM `prefix_" . $this->table . "` WHERE `id`='" . ( int ) $id . "'";
        $vote = SqlTools::selectRow ( $sql, MYSQLI_ASSOC );

        $sql = "SELECT `id`, `text`, `count_votes` AS `count` FROM `prefix_" . $this->table_answers . "` WHERE `votes_id`='" . ( int ) $id . "' AND `deleted`='N'";
        $answers_text = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

        $vote['answers_text'] = $answers_text;

        $this->title = 'Редактировать - ' . self::name;
        $this->content = tpl ( 'modules/' . __CLASS__ . '/edit', array (
            'link'     => $this->GetLink ( 'Info' ),
            'action'   => $this->GetLink ( 'Save' ),
            'validate' => $this->GetLink ( 'Validate' ),
            'module'   => __CLASS__,
            'vote'     => $vote,
        ) );
    }

    public function Save ()
    {
        if ( !empty ( $_POST ) )
        {
            if ( !isset ( $_POST['votes']['id'] ) )
            {
                $name = $_POST['votes']['name'];
                $show = ((isset ( $_POST['votes']['show'] ) && $_POST['votes']['show'] == 'on') ? 'Y' : 'N');
                $no_result = ((isset ( $_POST['votes']['no_result'] ) && $_POST['votes']['no_result'] == 'on') ? 'Y' : 'N');
                $userversion = ((isset ( $_POST['votes']['userversion'] ) && $_POST['votes']['userversion'] == 'on') ? 'Y' : 'N');

                $answer_text = $_POST['answer_text'];

                if ( !empty ( $name ) && !empty ( $answer_text ) )
                {
                    $sql = "INSERT INTO `prefix_" . $this->table . "` (`name`, `show`, `no_result`, `userversion`) VALUES ('" . $name . "', '" . $show . "', '" . $no_result . "', '" . $userversion . "')";

                    $id = SqlTools::insert ( $sql );

                    $order = 1;
                    foreach ( $answer_text as $text )
                    {
                        if ( !empty ( $text ) )
                        {
                            $sql = "INSERT INTO `prefix_" . $this->table_answers . "` (`text`, `votes_id`, `order`) VALUES ('" . $text . "', " . $id . ", " . $order . ")";
                            SqlTools::insert ( $sql );
                            $order++;
                        }
                    }
                }
            }
            else
            {
                $id = ( int ) $_POST['votes']['id'];
                $name = $_POST['votes']['name'];
                $show = ((isset ( $_POST['votes']['show'] ) && $_POST['votes']['show'] == 'on') ? 'Y' : 'N');
                $no_result = ((isset ( $_POST['votes']['no_result'] ) && $_POST['votes']['no_result'] == 'on') ? 'Y' : 'N');
                $userversion = ((isset ( $_POST['votes']['userversion'] ) && $_POST['votes']['userversion'] == 'on') ? 'Y' : 'N');

                $answer_id = $_POST['answer_id'];
                $answer_text = $_POST['answer_text'];

                if ( !empty ( $name ) && !empty ( $answer_text ) )
                {
                    $sql = "UPDATE `prefix_" . $this->table . "` SET `name`='" . $name . "', `show`='" . $show . "', `no_result`='" . $no_result . "', `userversion`='" . $userversion . "' WHERE `id`='" . $id . "'";

                    SqlTools::execute ( $sql );

                    $sql = "UPDATE `prefix_" . $this->table_answers . "` SET `votes_id`='0' WHERE `votes_id`='" . $id . "' AND `deleted`='N'";
                    SqlTools::execute ( $sql );

                    $order = 1;
                    for ( $i = 0; $i < count ( $answer_text ); $i++ )
                    {
                        $answer_id = $answer_id[$i];
                        $text = $answer_text[$i];
                        $sql = "SELECT `count_votes` FROM `prefix_" . $this->table_answers . "` WHERE `id`='" . $answer_id . "'";
                        $count = SqlTools::selectValue ( $sql );

                        if ( !$answer_id )
                        {
                            $sql = "INSERT INTO `prefix_" . $this->table_answers . "` (`text`, `votes_id`, `order`) VALUES ('" . $text . "', " . $id . ", " . $order . ")";
                            SqlTools::insert ( $sql );
                        }
                        else
                        {
                            $sql = "UPDATE `prefix_" . $this->table_answers . "` SET `text`='" . $text . "', `count_votes`='" . $count . "', `votes_id`='" . $id . "', `order`=" . $order . " WHERE `id`='" . $answer_id . "'";
                            SqlTools::execute ( $sql );
                        }
                        $order++;
                    }

                    $sql = "DELETE FROM `prefix_" . $this->table_answers . "` WHERE `votes_id`='0'";
                    SqlTools::execute ( $sql );
                }
            }

            if ( isset ( $_POST['continue'] ) && $_POST['continue'] == 'on' && isset ( $id ) )
            {
                $link = $this->GetLink ( 'Edit' ) . '&id=' . $id;
                header ( "Location: " . $link );
                die ();
            }

            if ( isset ( $_POST['new'] ) && $_POST['new'] == 'on' )
            {
                $link = $this->GetLink ( 'Addnew' );
                header ( "Location: " . $link );
                die ();
            }

            $link = $this->GetLink ( 'Info' );
            header ( "Location: " . $link );
            die ();
        }
    }

    public function Delete ()
    {
        $id = (!empty ( $_GET['id'] )) ? $_GET['id'] : null;
        if ( !$id )
        {
            header ( "Location: " . $this->GetLink ( 'Info' ) );
            die ();
        }

        $sql = "UPDATE `prefix_" . $this->table . "` SET `deleted`='Y' WHERE `id`='" . $id . "'";
        SqlTools::execute ( $sql );

        $sql = "UPDATE `prefix_" . $this->table_answers . "` SET `deleted`='Y' WHERE `votes_id`='" . $id . "'";
        SqlTools::execute ( $sql );

        $sql = "UPDATE `prefix_" . $this->table_userversion . "` SET `deleted`='Y' WHERE `votes_id`='" . $id . "'";
        SqlTools::execute ( $sql );

        header ( "Location: " . $this->GetLink ( 'Info' ) );
        die ();
    }

    public function Validate ()
    {
        if ( !empty ( $_POST ) )
        {
            if ( mb_strlen ( $_POST['votes']['name'] ) < 1 )
            {
                $error['name'] = 'Напишите Ваш вопрос';
            }
            if ( empty ( $_POST['answer_text'] ) || (count ( $_POST['answer_text'] ) == 1 && empty ( $_POST['answer_text'][0] )) )
            {
                $error['answer_text'] = 'Должен быть минимум один вариант ответа';
            }
        }

        if ( !empty ( $error ) )
        {
            $result = array (
                'error' => $error
            );
            exit ( json_encode ( $result ) );
        }
        else
        {
            $result = array (
                'success' => true
            );
            exit ( json_encode ( $result ) );
        }
    }

    public function Userversion ()
    {
        $sql = "SELECT tu.*, t.`name` AS `vote` FROM `prefix_" . $this->table_userversion . "` as tu LEFT JOIN `prefix_" . $this->table . "` AS t ON t.`id`=tu.`votes_id` WHERE tu.`deleted`='N'";
        $userversions = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

        $this->title = 'Варианты пользователей';
        $this->content = tpl ( 'modules/' . __CLASS__ . '/userversion_list', array (
            'module'       => __CLASS__,
            'edit'         => $this->GetLink ( 'Userversion_Edit' ),
            'delete'       => $this->GetLink ( 'Userversion_Delete' ),
            'userversions' => $userversions
        ) );
    }

    public function Userversion_Edit ()
    {
        $id = (!empty ( $_GET['id'] )) ? ( int ) $_GET['id'] : null;

        if ( $id )
        {
            $sql = "SELECT * FROM `prefix_" . $this->table_userversion . "` WHERE `id`='" . $id . "'";
            $version = SqlTools::selectRow ( $sql );

            $this->title = 'Варианты пользователей';
            $this->content = tpl ( 'modules/' . __CLASS__ . '/userversion_edit', array (
                'link'    => $this->GetLink ( 'Userversion' ),
                'action'  => $this->GetLink ( 'Userversion_Save' ),
                'module'  => __CLASS__,
                'version' => $version
            ) );
            return true;
        }
        else
        {
            header ( "Location: " . $this->GetLink ( 'Userversion' ) );
            die ();
        }
    }

    public function Userversion_Delete ()
    {
        $id = (!empty ( $_GET['id'] )) ? ( int ) $_GET['id'] : null;

        if ( $id )
        {
            $sql = "UPDATE `prefix_" . $this->table_userversion . "` SET `deleted`='Y' WHERE `id`='" . $id . "'";
            SqlTools::execute ( $sql );
        }

        header ( "Location: " . $this->GetLink ( 'Userversion' ) );
        die ();
    }

    public function Userversion_Save ()
    {
        $id = $_POST['version']['id'];
        $text = $_POST['version']['text'];
        if ( $id && $text )
        {
            $sql = "UPDATE `prefix_" . $this->table_userversion . "` SET `text`='" . $text . "' WHERE `id`='" . $id . "'";
            SqlTools::execute ( $sql );
        }

        if ( isset ( $_POST['continue'] ) && $_POST['continue'] == 'on' && isset ( $id ) )
        {
            $link = $this->GetLink ( 'Userversion_Edit' ) . '&id=' . $id;
            header ( "Location: " . $link );
            die ();
        }

        $link = $this->GetLink ( 'Userversion' );
        header ( "Location: " . $link );
        die ();
    }

    public function Settings ()
    {
        if ( !empty ( $_POST ) )
        {
            $sql = "SELECT * FROM `prefix_settings` WHERE `module`='" . __CLASS__ . "'";
            $settings = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

            if ( !empty ( $settings ) )
            {
                $sql = "DELETE FROM `prefix_settings` WHERE `module`='" . __CLASS__ . "'";
                SqlTools::execute ( $sql );
            }
            $settings = $_POST['settings'];
            foreach ( $settings as $callname => $value )
            {
                $sql = "INSERT INTO `prefix_settings` (`module`,`name`,`callname`,`value`) VALUES ('" . __CLASS__ . "', '', '" . $callname . "', '" . $value . "')";
                SqlTools::execute ( $sql );
            }

            header ( "Location: " . $this->GetLink ( 'Info' ) );
            die ();
        }

        $sql = "SELECT `callname`, `value` FROM `prefix_settings` WHERE `module`='" . __CLASS__ . "'";
        $result = SqlTools::selectRows ( $sql, MYSQLI_ASSOC );

        foreach ( $result as $row )
        {
            $settings[$row['callname']] = $row['value'];
        }

        $this->title = 'Настройки - ' . self::name;
        $this->content = tpl ( 'modules/' . __CLASS__ . '/settings', array (
            'link'     => $this->GetLink ( 'Settings' ),
            'module'   => __CLASS__,
            'settings' => $settings,
        ) );
    }

    public function Trash ()
    {
        $action = (!empty ( $_GET['action'] )) ? $_GET['action'] : null;
        if ( $action == 'clear' )
        {
            $sql = "DELETE FROM `prefix_" . $this->table . "` WHERE `deleted`='Y'";
            SqlTools::execute ( $sql );

            $sql = "DELETE FROM `prefix_" . $this->table_answers . "` WHERE `deleted`='Y'";
            SqlTools::execute ( $sql );

            $sql = "DELETE FROM `prefix_" . $this->table_userversion . "` WHERE `deleted`='Y'";
            SqlTools::execute ( $sql );

            header ( "Location: " . $this->GetLink ( 'Trash' ) );
            die ();
        }
        if ( $action == 'restore' )
        {
            $id = (!empty ( $_GET['id'] )) ? ( int ) $_GET['id'] : null;
            $table = (!empty ( $_GET['table'] )) ? $_GET['table'] : null;

            if ( $id && $table )
            {
                if ( $table == 'votes' )
                {
                    $sql = "UPDATE `prefix_" . $this->table . "` SET `deleted`='N' WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );
                }
                elseif ( $table == 'answers' )
                {
                    $sql = "UPDATE `prefix_" . $this->table_answers . "` SET `deleted`='N' WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );

                    $sql = "SELECT `votes_id` FROM `prefix_" . $this->table_answers . "` WHERE `id`='" . $id . "'";
                    $votes_id = SqlTools::selectValue ( $sql );

                    $sql = "UPDATE `prefix_" . $this->table . "` SET `deleted`='N' WHERE `id`='" . $votes_id . "'";
                    SqlTools::execute ( $sql );
                }
                elseif ( $table == 'userversion' )
                {
                    $sql = "UPDATE `prefix_" . $this->table_userversion . "` SET `deleted`='N' WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );

                    $sql = "SELECT `votes_id` FROM `prefix_" . $this->table_userversion . "` WHERE `id`='" . $id . "'";
                    $votes_id = SqlTools::selectValue ( $sql );

                    $sql = "UPDATE `prefix_" . $this->table . "` SET `deleted`='N' WHERE `id`='" . $votes_id . "'";
                    SqlTools::execute ( $sql );
                }
            }

            header ( "Location: " . $this->GetLink ( 'Trash' ) );
            die ();
        }

        if ( $action == 'delete' )
        {
            $id = (!empty ( $_GET['id'] )) ? ( int ) $_GET['id'] : null;
            $table = (!empty ( $_GET['table'] )) ? $_GET['table'] : null;

            if ( $id && $table )
            {
                if ( $table == 'votes' )
                {
                    $sql = "DELETE FROM `prefix_" . $this->table . "` WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );

                    $sql = "DELETE FROM `prefix_" . $this->table_answers . "` WHERE `votes_id`='" . $id . "'";
                    SqlTools::execute ( $sql );

                    $sql = "DELETE FROM `prefix_" . $this->table_userversion . "` WHERE `votes_id`='" . $id . "'";
                    SqlTools::execute ( $sql );
                }
                elseif ( $table == 'answers' )
                {
                    $sql = "DELETE FROM `prefix_" . $this->table_answers . "` WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );
                }
                elseif ( $table == 'userversion' )
                {
                    $sql = "DELETE FROM `prefix_" . $this->table_userversion . "` WHERE `id`='" . $id . "'";
                    SqlTools::execute ( $sql );
                }
            }

            header ( "Location: " . $this->GetLink ( 'Trash' ) );
            die ();
        }

        $sql = "SELECT * FROM `prefix_" . $this->table . "` WHERE `deleted`='Y'";
        $result = SqlTools::selectRows ( $sql );
        foreach ( $result as $row )
        {
            $trash[] = array (
                'id'    => $row['id'],
                'name'  => $row['name'],
                'type'  => 'Опросы',
                'table' => 'votes'
            );
        }

        $sql = "SELECT * FROM `prefix_" . $this->table_answers . "` WHERE `deleted`='Y'";
        $result = SqlTools::selectRows ( $sql );
        foreach ( $result as $row )
        {
            $trash[] = array (
                'id'    => $row['id'],
                'name'  => $row['text'],
                'type'  => 'Варианты ответов',
                'table' => 'answers'
            );
        }

        $sql = "SELECT * FROM `prefix_" . $this->table_userversion . "` WHERE `deleted`='Y'";
        $result = SqlTools::selectRows ( $sql );
        foreach ( $result as $row )
        {
            $trash[] = array (
                'id'    => $row['id'],
                'name'  => $row['text'],
                'type'  => 'Варианты ответов',
                'table' => 'userversion'
            );
        }

        $this->title = 'Корзина - ' . self::name;
        $this->content = tpl ( 'modules/' . __CLASS__ . '/trash', array (
            'link'    => $this->GetLink ( 'Trash' ),
            'clear'   => $this->GetLink ( 'Trash' ) . '&action=clear',
            'restore' => $this->GetLink ( 'Trash' ) . '&action=restore',
            'delete'  => $this->GetLink ( 'Trash' ) . '&action=delete',
            'module'  => __CLASS__,
            'trash'   => $trash,
        ) );
    }
}