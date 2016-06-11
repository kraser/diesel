<?php

class Tools
{
    /**
     * <pre>Выводит текст в стандартный поток ошибок</pre>
     * @param String $text <p>Выводимое сообщение</p>
     * @param Integer $stackLevel <p>Глубина вложенности вызова до прикладного кода</p>
     */
    public static function notify ( $text, $stackLevel = 0 )
    {
        $stack = debug_backtrace ();
        if ( array_key_exists ( $stackLevel, $stack ) )
        {
            $file = basename ( $stack[$stackLevel]['file'] );
            $line = basename ( $stack[$stackLevel]['line'] );
            $trace = $file . ':' . $line;
        }
        else
            $trace = '';

        $time = date ( 'Y-m-d H:i:s' );
        $stderr = @fopen ( 'php://stderr', 'w' );
        fwrite ( $stderr, "$time\t[$trace]\t$text\n" );
        fclose ( $stderr );
    }

    /**
     * <pre>Выводит в стандартный поток ошибок информацию о цепочке вызовов<pre>
     */
    public static function backTrace ()
    {
        $stack = debug_backtrace ();
        foreach ( $stack as $step )
        {
            $fileInfo = array_key_exists ( 'file', $step ) ? $step['file'] : 'NOT_TRACED';
            $lineInfo = array_key_exists ( 'line', $step ) ? $step['line'] : 'NOT_TRACED';
            self::notify ( $fileInfo . ':' . $lineInfo );
        }
    }

    /**
     * <pre>Выводит в стандартный поток ошибок сериализованную переменную</pre>
     * @param Mixed $var <p>Выводимая переменнная</p>
     */
    public static function dump ( $var )
    {
        self::notify ( serialize ( $var ), 1 );
    }

    /**
     * <pre>Выводит в заданный файл сериализованную переменную</pre>
     * @param Mixed $data <p>Выводимая переменная</p>
     * @param String $fileName <p>Имя файла</p>
     */
    public static function dumpToFile ( $data, $fileName = 'dump' )
    {
        file_put_contents ( $fileName, serialize ( $data ) );
    }

    /**
     * <pre>Время выполения части скрипта</pre>
     * @param String $partname <p>Имя засекаемого блока</p>
     * @param Boolean $stop <p>Окончание измерения</p>
     * @return void
     */
    public static function timegen ( $partname = '', $stop = false )
    {
        if ( DEBUG === false )
            return false;

        if ( !isset ( $GLOBALS['timegen'] ) )
            $GLOBALS['timegen'] = array ();

        if ( !isset ( $GLOBALS['timegen'][$partname] ) )
            $GLOBALS['timegen'][$partname] = array ();

        if ( !$stop )
        {
            $GLOBALS['timegen'][$partname]['start'] = microtime ( true );
            return true;
        }
        else
            $GLOBALS['timegen'][$partname]['time'] = microtime ( true ) - $GLOBALS['timegen'][$partname]['start'];

        if ( !isset ( $GLOBALS['timegen'][$partname]['result'] ) )
            $GLOBALS['timegen'][$partname]['result'] = 0;

        $GLOBALS['timegen'][$partname]['result'] += $GLOBALS['timegen'][$partname]['time'];

        return $GLOBALS['timegen'][$partname]['time'];
    }

    static function getSettings ( $module, $callname, $default = '' )
    {
        if ( empty ( $GLOBALS['modulesSettings'] ) )
        {
            $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_settings`", MYSQL_ASSOC );
            foreach ( $rows as $row )
            {
                $GLOBALS['modulesSettings'][$row['module']][$row['callname']] = $row;
            }
        }

        if ( isset ( $GLOBALS['modulesSettings'][$module][$callname] ) )
            return $GLOBALS['modulesSettings'][$module][$callname]['value'];
        else
            return $default;
    }

    /**
     * Отображает блок с результатами измерений
     *
     * @return void
     */
    public static function timegen_result ()
    {
        if ( !Starter::app ()->develop )
            return false;

        if ( !isset ( $GLOBALS['timegen'] ) || !is_array ( $GLOBALS['timegen'] ) || count ( $GLOBALS['timegen'] ) == 0 )
        {
            echo '
            <script type="text/javascript">
                if(typeof(console) != "undefined") {
                    console.log("' . $GLOBALS['config']['msg']['timegen_nb'] . '");
                }
            </script>';

            return false;
        }

        $log = array ();
        $log[] = $GLOBALS['config']['msg']['timegen'];
        foreach ( $GLOBALS['timegen'] as $k => $v )
        {
            if ( !isset ( $v['time'] ) )
            {
                $current_result = microtime ( true ) - $v['start'];
                $log[] = $k . ': ' . sprintf ( '%.5f', $current_result ) . ' ' . $GLOBALS['config']['msg']['timegen_ns'];
            }
            else
                $log[] = $k . ': ' . sprintf ( '%.5f', $v['result'] );
        }

        $log[] = 'Текущее количество запросов: ' . $GLOBALS['query_count'];
        if ( $GLOBALS['config']['db']['show_query_devmode'] )
            echo implode ( '<br />', $GLOBALS['query_log'] );

        // Память
        $log[] = 'Пик памяти: ' . bytes_to_str ( memory_get_peak_usage () );

        echo '
		<script type="text/javascript">
			if(typeof(console) != "undefined") {
				console.log(' . json_encode ( $log ) . ');
			}
		</script>';
    }

    public static function getBackTraceLine ()
    {

    }

    /**
     * <pre>Возвращает путь к mime иконке для переданного расширения,
     * если нет такой - пустую строку</pre>
     * @param String $ext <p>Расширение</p>
     * @return String <p>Полный путь к иконке MIME-типа</p>
     */
    public static function getMimeIconForExt ( $ext )
    {
        $mimeIcons = array (
            'txt' => 'txt.png',
            'rtf' => 'txt.png',
            'doc' => 'doc.png',
            'docx' => 'doc.png',
            'xls' => 'xls.png',
            'xlsx' => 'xls.png',
            'pdf' => 'pdf.png',
            'ppt' => 'ppt.png',
            'zip' => 'zip.png',
            'rar' => 'rar.png',
            'gz' => 'pack.png',
            'tar' => 'pack.png',
            'mp3' => 'mp3.png',
            'avi' => 'video.png',
            '' => 'empty.png',
        );

        $ext = strtolower ( $ext );
        $ret = isset ( $mimeIcons[$ext] ) ? $mimeIcons[$ext] : $mimeIcons[''];
        if ( file_exists ( Starter::getAliasPath ( "webroot.site") . DS . Starter::app()->getTheme() . DS . "assets/images/mimetypes/$ret" ) )
            $icon = DS . SITE . DS . Starter::app()->getTheme() . DS . "assets/images/mimetypes/$ret";
        else
            $icon = MIMETYPES . DS . $ret;

        return $icon;
    }

    /**
     * <pre>Возвращает массив родительских категорий
     * и переданной категории $cat, корневые - в конце,
     * переданная категория - в начале</pre>
     * @param String $category
     * @return Array
     */
    public static function getParentCategories ( $category )
    {
        $ret = array ();

        while ( !empty ( $category ) )
        {
            array_push ( $ret, $category );
            $query = "SELECT `top` FROM `prefix_products_topics` WHERE `id`='" . ( int ) $category . "' AND `deleted`='N'";
            $category = SqlTools::selectValue ( $query );
        }

        return $ret;
    }

    /**
     * <pre>Возвращает массив дочерних категорий
     * и переданной категории $cat,
     * переданная категория - в начале</pre>
     * @param Array $category
     * @return Array
     */
    public static function getChildrenCategories ( $category )
    {
        $ret = array ();
        if ( !isset ( $category ) )
            return $ret;

        $category = ( int ) $category;
        array_unshift ( $ret, $category );

        $query = "SELECT `id` FROM `prefix_products_topics` WHERE `top`='" . ( int ) $category . "' AND `deleted`='N'";
        $categories = SqlTools::selectRows ( $query, MYSQL_ASSOC );
        $categories = ArrayTools::pluck ( $categories, 'id' );
        foreach ( $categories as $key => $cat )
        {
            $cats = self::getChildrenCategories ( ( int ) $cat );
            $ret = array_merge ( $ret, $cats );
        }

        return $ret;
    }

    /**
     * <pre>Возвращает массив записей характеристик родительских категорий
     * и переданной категории $cat (ассоциированных по MYSQL_ASSOC),
     * сортированных по категориям: корневые - в начале,
     * переданная категория - в конце</pre>
     * @param Array $category
     * @return Array
     */
    public static function getParentCategoriesTags ( $category )
    {
        $tags = array ();

        $cats = Tools::getParentCategories ( $category );
        $query = "SELECT * FROM `prefix_tags_values` "
            . " WHERE `module`='Catalog' AND `moduleId` IN (" . implode ( ',', $cats ) . ") "
            . " AND `show`='Y' "
            . " GROUP BY `tagId`"
            . " ORDER BY `moduleId` DESC";
        $tags = SqlTools::selectRows ( $query, MYSQL_ASSOC, 'tagId' );

        return $tags;
    }
}
