<?php

/**
 * Класс реализующий режим Language
 */
class Language
{
    private static $instance;
    private static $default_site = 1;
    private static $default_admin = 1;
    private static $data = array ();

    /**
     * Конструктор режима Languages
     */
    public function __construct ()
    {
        self::$data = SqlTools::selectRows ( "SELECT * FROM `prefix_languages_translate`", MYSQL_ASSOC, 'translate1' );
    }

    /**
     * Возвращает синглтон класса User
     * @return User
     */
    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function Init ()
    {
        $instance = self::getInstance ();
        self::Run ();
    }

    /**
     * Запуск режима Language
     * @return void
     * @throws Exception
     */
    public function Run ()
    {
        if ( MODE == 'Site' && !isset ( $_SESSION['site_language'] ) )
        {
            $_SESSION['site_language'] = self::$default_site;
        }

        if ( MODE == 'Admin' && !isset ( $_SESSION['admin_language'] ) )
        {
            $_SESSION['admin_language'] = self::$default_admin;
        }

        if ( isset ( $_REQUEST['language_id'] ) )
        {
            if ( MODE == 'Site' )
            {
                $_SESSION['site_language'] = $_REQUEST['language_id'];
            }
            elseif ( MODE == 'Admin' )
            {
                $_SESSION['admin_language'] = $_REQUEST['language_id'];
            }
            define ( _LANG, $_REQUEST['language_id'] );
        }
        else
        {
            if ( MODE == 'Site' )
            {
                define ( _LANG, $_SESSION['site_language'] );
            }
            elseif ( MODE == 'Admin' )
            {
                define ( _LANG, $_SESSION['admin_language'] );
            }
        }

//        $admin_lang = SqlTools::selectRow("SELECT id, code FROM `prefix_languages` WHERE `base_admin` = 'Y'", MYSQL_ASSOC);
//        define(_LANG_CODE, $admin_lang['code']);
//        define(_LANG_ADMIN, $admin_lang['id']);
//        self::addFields();
//        self::deleteFields();
    }

    public function _t ()
    {
        $args = func_get_args ();
        if ( count ( $args ) == 0 )
        {
            return false;
        }

        $key = array_splice ( $args, 0, 1 );
        $key = $key[0];

        if ( !isset ( self::$data[$key] ) && MODE == 'Site' )
        {
            SqlTools::execute ( "INSERT INTO `prefix_languages_translate` (`translate1`) VALUES ('" . $key . "')" );
        }

        if ( MODE == 'Site' )
        {
            $lang_id = _LANG;
        }
        elseif ( MODE == 'Admin' )
        {
            $lang_id = _LANG_ADMIN;
        }

        if ( isset ( self::$data[$key] ) && !empty ( self::$data[$key]['translate' . $lang_id] ) )
        {
            $value = self::$data[$key]['translate' . $lang_id];
        }
        else
        {
            $value = $key;
        }

        if ( strpos ( $value, '%s' ) && count ( $args ) > 0 )
        {
            // array_splice($args, 0, substr_count($value, '%s'));
            $value = vsprintf ( $value, $args );
        }

        return $value;
    }

    public function languagesBar ()
    {
        if ( MODE == 'Site' )
        {
            $tpl = 'parts/languages';
            $language_id = $_SESSION['site_language'];
        }
        elseif ( MODE == 'Admin' )
        {
            $tpl = 'widgets/languages';
            $language_id = $_SESSION['admin_language'];
        }
        $languages = SqlTools::selectObjects ( "SELECT * FROM `prefix_languages`" );
        return TemplateEngine::view ( $tpl, array (
                'languages' => $languages,
                'language_id' => $language_id,
        ) );
    }

    private function addFields ( $language_id = null )
    {
        if ( !empty ( $language_id ) )
        {
            $where = ' AND `id` = ' . ( int ) $language_id;
        }
        $languages = SqlTools::selectRows ( "SELECT * FROM `prefix_languages` WHERE 1 " . $where, MYSQL_ASSOC );
        $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_languages_fields`", MYSQL_ASSOC );

        foreach ( $rows as $row )
        {
            for ( $i = 1; $i < count ( $languages ); $i++ )
            {
                try
                {
                    SqlTools::execute ( "ALTER TABLE `prefix_" . $row['table'] . "` ADD COLUMN `" . $row['field'] . $languages[$i]['id'] . "` " . $row['attributes'] . " AFTER `" . $row['field'] . ($languages[$i - 1]['id']) . "`;" );
                }
                catch ( Exception $ex )
                {
                    $code = $ex->getCode ();
                    if ( $code != 1060 )
                    {
                        exit ( $ex->getMessage () );
                    }
                }
            }
        }
    }

    private function deleteFields ( $language_id = null )
    {
        if ( !empty ( $language_id ) )
        {
            $where = ' AND `id` = ' . ( int ) $language_id;
        }
        $languages = SqlTools::selectRows ( "SELECT * FROM `prefix_languages` WHERE id <> 1 " . $where, MYSQL_ASSOC );
        $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_languages_fields`", MYSQL_ASSOC );

        foreach ( $rows as $row )
        {
            for ( $i = 0; $i < count ( $languages ); $i++ )
            {
                try
                {
                    SqlTools::execute ( "ALTER TABLE `prefix_" . $row['table'] . "` DROP `" . $row['field'] . $languages[$i]['id'] . "`;" );
                }
                catch ( Exception $ex )
                {
                    $code = $ex->getCode ();
                    if ( $code != 1091 )
                    {
                        exit ( $ex->getMessage () );
                    }
                }
            }
        }
    }
}
