<?php

class AdminModule
{
    /**
     * Нужно ли скрыть модуль
     * @var bool
     */
    const hide = false;

    /**
     * Определяет путь до шаблонов модуля
     * @var string
     */
    protected $tpls;

    /**
     * Заголовок/название действия модуля
     * @var string
     */
    public $title;

    /**
     * Содержимое действия модуля
     * @var string
     */
    public $content;

    /**
     * Содержание для подсказки справа от таблицы
     * @var array(title,text,link)
     */
    public $hint;
    public $submenu = array ();

    /**
     * Имя класса
     * @var String
     */
    protected $called_class;

    function __construct ()
    {
        $this->called_class = get_called_class ();
        $this->tpls = 'modules/' . __CLASS__ . '/';

        //Вдруг у модуля есть настройки
        if ( ! method_exists ( $this, 'Settings' ) )
        {
            $settings = SqlTools::selectRows ( "SELECT * FROM `prefix_settings` WHERE `module` = '" . $this->called_class . "'" );
            if ( count ( $settings ) > 0 )
                $this->submenu['_Settings'] = 'Настройки модуля';
        }

        //todo refactoring вызов через Reflection
        $method = filter_input ( INPUT_GET, "method", FILTER_SANITIZE_STRING );
        if ( $method )
        {
            if ( method_exists ( $this, $method ) )
                $this->$method ();
            else if ( method_exists ( $this, 'Info' ) )
                $this->Info ();
        }
        else
        {
            if ( method_exists ( $this, 'Info' ) )
                $this->Info ();
        }
    }

    /**
     * Отдает список с настройками текущего модуля
     */
    function _Settings ()
    {
        // выпилить globals
        global $modules;

        $table = 'settings';

        $modulesForSelect = array ();
        foreach ( $modules as $m )
        {
            if ( !$m['hide'] )
            {
                $modulesForSelect[] = $m['module'];
            }
        }

        $this->title = 'Настройки модуля ' . $this->called_class;
        $this->content = $this->DataTable (
            $table, array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'order' => 'order'  // INT
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit'
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'module' => array (
                'name' => 'Модуль',
                'length' => '0-32',
                'autocomplete' => $modulesForSelect,
                'default' => $this->called_class
            ),
            'name' => array ( 'name' => 'Название настройки', 'length' => '1-128' ),
            'callname' => array ( 'name' => 'Имя для вызова', 'length' => '0-128' ),
            'value' => array (
                'name' => 'Значение',
                'autocomplete' => 'autocomplete_table',
            )
            ), "`module` = '" . $this->called_class . "'"
        );
    }

    /**
     * Таб с сеошными данными
     * title, keywords, description
     */
    function _Seo ()
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $module = get_called_class ();
        $message = '';

        $data = SqlTools::selectRow ( "SELECT * FROM `prefix_seo` WHERE `module` = '$module' AND `module_id` = $id", MYSQL_ASSOC );

        if ( !empty ( $_POST ) )
        {
            if ( empty ( $data ) )
            {
                $query = "INSERT INTO `prefix_seo` (`module`, `module_id`, `module_table`, `title`, `keywords`, `description`, `tagH1`)
                    VALUES
					(
                        '$module',
                        $id,
                        '" . SqlTools::escapeString ( $_POST['table'] ) . "',
                        '" . SqlTools::escapeString ( $_POST['title'] ) . "',
                        '" . SqlTools::escapeString ( $_POST['keywords'] ) . "',
                        '" . SqlTools::escapeString ( $_POST['description'] ) . "',
                        '" . SqlTools::escapeString ( $_POST['tagH1'] ) . "'
                    )";

                if ( SqlTools::insert ( $query ) )
                    $message = 'SEO данные добавлены';
            }
            else
            {
                $query = "UPDATE `prefix_seo`
                    SET
                        `title` = '" . SqlTools::escapeString ( $_POST['title'] ) . "',
                        `keywords` = '" . SqlTools::escapeString ( $_POST['keywords'] ) . "',
                        `description` = '" . SqlTools::escapeString ( $_POST['description'] ) . "',
                        `tagH1` = '" . SqlTools::escapeString ( $_POST['tagH1'] ) . "'
					WHERE
                        `module` = '$module'
                        AND `module_id` = $id
                        AND `module_table` = '" . SqlTools::escapeString ( $_POST['table'] ) . "'";

                if ( SqlTools::execute ( $query ) )
                {
                    $message = 'SEO данные обновлены';
                }
            }
            $data = SqlTools::selectRow ( "SELECT * FROM `prefix_seo` WHERE `module` = '$module' AND `module_id` = $id" );
        }

        if ( empty ( $data ) )
            $data = array ( 'title' => '', 'keywords' => '', 'description' => '', 'tagH1' => '' );

        echo tpl ( 'widgets/seo', array (
            'title' => $data['title'],
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'tagH1' => $data['tagH1'],
            'link' => $this->GetLink ( '_Seo', array (), get_called_class () ),
            'message' => $message
        ) );
        exit ();
    }

    /**
     * Таб с регионами
     */
    function _Regions ()
    {
//        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
//        if ( $id == 0 )
//        {
//            echo 'Сначала создайте запись';
//            exit ();
//        }
//
//        $module = get_called_class ();
//        $message = '';
//
//        if ( !empty ( $_POST ) )
//        {
//            $selected_regions = $_POST['regions'];
////            $query = "DELETE FROM `prefix_module_to_region` WHERE `module` = '" . $module . "' AND `module_id` = '" . $id . "'";
////            $deleted = SqlTools::execute ( $query );
//
//            if ( ((isset ( $selected_regions ) && ($selected_regions[0] == 'all')) || empty ( $selected_regions )) && $deleted )
//            {
//                $message = 'Регионы обновлены';
//            }
//            else
//            {
//                $query = "INSERT INTO `prefix_module_to_region` (`region_id`, `module`, `module_id`) VALUES";
//                foreach ( $selected_regions as $region )
//                {
//                    $query .= " ('" . ( int ) $region['id'] . "', '" . $module . "', '" . $id . "') ";
//                    if ( $region !== end ( $selected_regions ) )
//                    {
//                        $query .= ", ";
//                    }
//                }
//                if ( SqlTools::execute ( $query ) )
//                    $message = 'Регионы обновлены';
//            }
//        }
//
//        $sql = "SELECT * FROM `prefix_regions` WHERE `deleted` = 'N'";
//        $regions = SqlTools::selectRows ( $sql, MYSQL_ASSOC );
//
//        $sql = "SELECT `region_id` FROM `prefix_module_to_region`"
//            . " WHERE `module` = '" . $module . "'"
//            . " AND `module_id` = '" . $id . "'";
//        $regions_active = SqlTools::selectRows ( $sql, MYSQL_ASSOC, 'region_id' );
//
//        $hasActive = false;
//        foreach ( $regions as &$region )
//        {
//            if ( isset ( $regions_active[$region['id']] ) )
//            {
//                $region['active'] = true;
//                $hasActive = true;
//            }
//            else
//            {
//                $region['active'] = false;
//            }
//        }
//
//        echo tpl ( 'widgets/regions', array (
//            'regions' => $regions,
//            'hasActive' => $hasActive,
//            'link' => $this->GetLink ( '_Regions', array (), get_called_class () ),
//            'message' => $message
//        ) );
        exit ();
    }

    /**
     * Генерация ссылки для админки
     *
     * @param string $method
     * @param array $data
     * @param string $module
     *
     * @return string
     */
    protected function GetLink ( $method = '', $data = array (), $module = '' )
    {
        $level = 1;
        $debug_info = debug_backtrace ();
        if ( $debug_info[$level]['class'] == 'AdminModule' && isset ( $debug_info[$level + 1] ) )
        {
            $level++;
        }

        $urlParts = array (
            'module' => empty ( $module ) ? $this->called_class : $module,
            'method' => empty ( $method ) ? $debug_info[$level]['function'] : $method
        );
        /*
          $level = 1;
          $debug_info = debug_backtrace();
          if($debug_info[$level]['class'] == 'AdminModule' && isset($debug_info[$level+1])) $level++;

          $url = array(
          'module'	=> empty($module)?$debug_info[$level]['class']:$module,
          'method'	=> empty($method)?$debug_info[$level]['function']:$method
          );
         */
        $url = array_merge ( $urlParts, $data );
        return '?' . http_build_query ( $url );
    }

    /**
     * Собираем данные о полях таблицы
     *
     * @param $table
     */
    function TableColumns ( $table )
    {
        $columns = SqlTools::selectRows ( "SHOW COLUMNS FROM `prefix_$table`", MYSQL_ASSOC );
        $syscolumns = array ();
        foreach ( $columns as $i )
        {
            $syscolumns[$i['Field']] = $i;
        }
        return $syscolumns;
    }

    /**
     * <pre>Оперирует с данными из таблиц базы данных (таблицы компонентов)</pre>
     * @param String $table <p>Имя таблицы (компонента)</p>
     * @param Array $options <p>Опции действий</p>
     * @param Array $fields <p>Список полей таблицы для операции</p>
     * @param String $where <p>Селекторы для SQL-запроса</p>
     * @param String $order <p>Поле и порядок сортировки</p>
     * @return String <p>Строка с таблицами</p>
     */
    function DataTable ( $table, $options = array (), $fields = array (), $where = '', $order = '' )
    {
        //Собираем данные о полях таблицы
        $syscolumns = $this->TableColumns ( $table );
        $deleted_field_exist = false;
        if ( isset ( $options['nouns']['deleted'] ) )
        {
            foreach ( $syscolumns as $i )
            {
                if ( $i['Field'] == $options['nouns']['deleted'] )
                {
                    $deleted_field_exist = true;
                }
            }
        }

        //Удаление
        if ( isset ( $_GET['delete'] ) )
        {
            if ( $deleted_field_exist )
            {
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['deleted']}` = 'Y' "
                    . " WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['delete'] );
            }
            else
            {
                SqlTools::execute ( "DELETE FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['delete'] );
            }

            //Удаление записей подопечной таблицы
            if ( isset ( $options['inner']['table'] ) )
            {
                if ( isset ( $options['inner']['deleted'] ) )
                {
                    SqlTools::execute ( "UPDATE `prefix_{$options['inner']['table']}` SET `{$options['inner']['deleted']}` = 'Y' WHERE "
                        . " `{$options['inner']['top_key']}` IN (" . $_GET['delete'] . ")" );
                }
                else
                {
                    SqlTools::execute ( "DELETE FROM `prefix_{$options['inner']['table']}` "
                        . " WHERE `{$options['inner']['top_key']}` IN (" . $_GET['delete'] . ")" );
                }
            }

            //Ссылка для возврата назад, похоже самое простое решение это убрать delete из запроса
            $plink = preg_replace ( '/&?delete=[\d]+/i', '', $_SERVER['REQUEST_URI'] );
            header ( 'Location: ' . $plink );
        }

        //Сохранение сортировки
        if ( isset ( $_POST['tr' . $table] ) && is_array ( $_POST['tr' . $table] ) )
        {

            foreach ( $_POST['tr' . $table] as $k => $v )
            {
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['order']}` = $k  WHERE `{$options['nouns']['id']}` = $v" );
            }
            exit ();
        }

        //Редактирование данных из таблицы (аякс)
        if ( isset ( $_GET['update'] ) )
        {
            SqlTools::execute ( "UPDATE `prefix_" . SqlTools::escapeString ( $_POST['table'] ) . "` SET `" . SqlTools::escapeString ( $_POST['field'] ) . "` = '" . SqlTools::escapeString ( $_POST['val'] ) . "' WHERE `{$options['nouns']['id']}` = " . ( int ) $_POST['id'] );
            exit ();
        }

        //Редактирование текстового содержания
        if ( isset ( $_GET['edit_text'] ) )
        {

            //Сохранение
            if ( isset ( $_POST['text'] ) )
            {
                $newtext = SqlTools::escapeString ( $_POST['text'] );
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['text']}` = '$newtext' WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['edit_text'] );
            }

            $i = SqlTools::selectRows ( "SELECT `{$options['nouns']['id']}`,`{$options['nouns']['name']}`,`{$options['nouns']['text']}` FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['edit_text'] );
            $text = $i[0][$options['nouns']['text']];
            $name = $i[0][$options['nouns']['name']];
            $id = $i[0][$options['nouns']['id']];

            //Ссылка для возврата назад, похоже самое простое решение это убрать edit_text из запроса
            $plink = preg_replace ( '/&?edit_text=[\d]+/i', '', $_SERVER['REQUEST_URI'] );

            $ret = tpl ( 'widgets/table_edit_text', array (
                'plink' => $plink,
                'text' => $text,
                'name' => $name,
                'id' => $id
                ) );

            $this->title = 'Редактирование «' . $name . '»';

            $this->hint = array (
                'title' => 'О редактировании текстов',
                'text' => '
					<p>Вы можете использовать блоки в формате <code>{block:0}</code> их можно добавить в разделе <a href="' . $this->GetLink ( 'Info', array (), 'Blocks' ) . '">блоков</a></p>'
            );

            return $ret;
        }

        if ( empty ( $where ) )
        {
            $where = '1';
        }

        if ( !empty ( $fields ) )
        {
            // ?? что тут должно делать с полями
        }

        if ( isset ( $_GET['order'] ) && in_array ( $_GET['order'], array_keys ( $syscolumns ) ) )
        {
            $order = SqlTools::escapeString ( $_GET['order'] );
        }

        if ( !isset ( $_GET['order'] ) && isset ( $options['nouns']['order'] ) && in_array ( $options['nouns']['order'], array_keys ( $syscolumns ) ) && in_array ( $options['nouns']['order'], array_keys ( $fields ) ) )
        {
            $order = $options['nouns']['order'];
        }

        if ( empty ( $order ) )
        {
            $order = $options['nouns']['id'];
        }

        if ( isset ( $_GET['orderd'] ) && in_array ( $_GET['orderd'], array ( 'ASC', 'DESC' ) ) )
        {
            $orderd = $_GET['orderd'];
        }
        else
        {
            $orderd = 'ASC';
        }

        if ( $deleted_field_exist )
        {
            $where .= " AND `{$options['nouns']['deleted']}` = 'N'";
        }

        $tableRows = SqlTools::selectRows ( "SELECT * FROM `prefix_$table` WHERE $where ORDER BY `$order` $orderd", MYSQL_ASSOC );

        $rows = array ();
        /** опции для управления отображением записей в таблице */
        $rowsOpts = array ();
        $header = array ();

        foreach ( $tableRows as $i )
        {
            if ( !count ( $header ) )
            {
                foreach ( $i as $k => $v )
                {
                    if ( empty ( $fields ) )
                    {
                        $header[$k] = array (
                            'field' => $k,
                            'name' => $k
                        );
                    }
                    else
                    {
                        if ( in_array ( $k, array_keys ( $fields ) ) && !isset ( $fields[$k]['hide_from_table'] ) )
                        {
                            $header[$k] = array (
                                'field' => $k,
                                'name' => $fields[$k]['name'],
                                'class' => isset ( $fields[$k]['class'] ) ? $fields[$k]['class'] : '',
                                'shortLabel' => isset ( $fields[$k]['shortLabel'] ) ? $fields[$k]['shortLabel'] : ''
                            );
                        }
                    }
                    if ( $order == $k )
                    {
                        $header[$k] = array_merge ( $header[$k], array ( 'order' => true, 'orderd' => $orderd ) );
                    }

                    if ( isset ( $fields[$k]['style'] ) )
                    {
                        $header[$k]['style'] = $fields[$k]['style'];
                    }
                }
                //Страница контента (Принадлежит)
                if ( isset ( $options['nouns']['holder'] ) )
                {
                    $header[$options['nouns']['holder']] = array
                    (
                        'field' => $options['nouns']['holder'],
                        'name' => 'Принадлежит',
                        'class' => ''
                    );
                }
            }

            $row = array ();
            $select_replace_text = array ();
            // по каждому полю
            foreach ( $i as $field => $value )
            {
                if ( (in_array ( $field, array_keys ( $fields ) ) && !isset ( $fields[$field]['hide_from_table'] )) || empty ( $fields ) )
                {

                    //МОДИФИКАЦИЯ ДАННЫХ В ТАБЛИЦЕ
                    //Модификация через пользовательскую функцию (transform)
                    if ( isset ( $fields[$field]['transform'] ) )
                    {
                        if ( is_object ( $fields[$field]['transform'] ) )
                        {
                            $row[$field] = $fields[$field]['transform'] ( $value, $i );
                            continue;
                        }
                        elseif ( method_exists ( $this, $fields[$field]['transform'] ) )
                        {
                            $row[$field] = $this->$fields[$field]['transform'] ( $value, $i );
                            continue;
                        }
                    }

                    //Селект
                    // если поле заявлено как селект (select) - нужно его значения выбирать из другой таблицы
                    // ['select']['table'] - имя таблицы, из которой выбирать
                    // ['select']['name'] - поле (или массив из массива 'fields' - поля
                    // и элемента 'delim' - разделитель между значениями для отображения),
                    // которое(ые) будет(ут) отображаться в списке формы
                    // ['select']['id'] - поле ИД - первичного ключа
                    if ( isset ( $fields[$field]['multiselect'] ) )
                    {
                        $fields[$field]['select'] = $fields[$field]['multiselect'];
                    }
                    if ( isset ( $fields[$field]['select'] ) && isset ( $fields[$field]['select']['table'] ) && isset ( $fields[$field]['select']['name'] ) )
                    {
                        if ( !count ( $select_replace_text ) && !isset ( $select_replace_text[$fields[$field]['select']['table']] ) )
                        {
                            if ( isset ( $fields[$field]['select']['deleted'] ) )
                            { // признак не удалённых - наличие элемента deleted, его значение - имя поля признака удалённости
                                $is_deleted = "AND `{$fields[$field]['select']['deleted']}` = 'N'";
                            }
                            else
                            {
                                $is_deleted = '';
                            }
                            // выбираются все не deleted
                            $selectsRow = SqlTools::selectRows ( "SELECT * FROM `prefix_{$fields[$field]['select']['table']}` "
                                    . " WHERE 1 $is_deleted", MYSQL_ASSOC );
                            foreach ( $selectsRow as $si )
                            {
                                // $skey - значение идентифицирующего поля таблицы (первичного ключа или наименования)
                                if ( isset ( $fields[$field]['select']['id'] ) )
                                {
                                    $skey = $si[$fields[$field]['select']['id']];
                                }
                                else
                                {
                                    if ( is_array ( $fields[$field]['select']['name'] ) )
                                    {
                                        $skey = $si[implode ( ", ", $fields[$field]['select']['name']['fields'] )];
                                    }
                                    else
                                    {
                                        $skey = $si[$fields[$field]['select']['name']];
                                    }
                                }
                                // если несколько полей (массив), наименования выбираем их слепленные значения и помещаем в $select_replace_text
                                // (похоже, считается, что в этом случае идент-щим полем может быть только id)
                                $fieldNames = array ();
                                if ( is_array ( $fields[$field]['select']['name'] ) )
                                {
                                    $fieldNames = $fields[$field]['select']['name']['fields'];
                                }
                                else
                                {
                                    $fieldNames[] = $fields[$field]['select']['name'];
                                }

                                if ( count ( $fieldNames ) > 1 )
                                {
                                    $arg = "";
                                    $fieldNames = $fields[$field]['select']['name']['fields'];
                                    $delim = ", '{$fields[$field]['select']['name']['delim']}', ";
                                    foreach ( $fieldNames as $fieldName )
                                    {
                                        $arg = $arg . "`" . $fieldName . "`" . $delim;
                                    }
                                    $concat = substr ( $arg, 0, strlen ( $arg ) - strlen ( $delim ) );

                                    $query = "SELECT CONCAT($concat) AS {$fieldNames[0]} FROM `prefix_{$fields[$field]['select']['table']}` "
                                        . " WHERE `id`=$skey";

                                    $datalist = SqlTools::selectRow ( $query );

                                    $select_replace_text[$fields[$field]['select']['table']] [$skey] = $datalist[$fieldNames[0]];
                                }
                                else
                                {
                                    $select_replace_text[$fields[$field]['select']['table']] [$skey] = $si[$fieldNames[0]];
                                }
                            }
                        }
                        // присваиваем в массив записей для отображения
                        if ( isset ( $select_replace_text[$fields[$field]['select']['table']] [$value] ) )
                        {
                            $row[$field] = $select_replace_text[$fields[$field]['select']['table']] [$value];
                        }
                        else
                        {
                            $row[$field] = $value;
                        }

                        //Мультиселект
                        if ( isset ( $fields[$field]['multiselect'] ) )
                        {
                            $curr_values = @unserialize ( $value );
                            if ( is_array ( $curr_values ) )
                            {
                                $row_vals = array ();
                                foreach ( $curr_values as $mvalue )
                                {
                                    $row_vals[] = $select_replace_text[$fields[$field]['select']['table']] [$mvalue];
                                }
                                $row[$field] = implode ( ', ', $row_vals );
                            }
                        }

                        continue;
                    }

                    //Ссылка
                    if ( isset ( $fields[$field]['link'] ) )
                    {
                        $row[$field] = '<a href="' . str_replace ( '{id}', $i[$options['nouns']['id']], $fields[$field]['link'] ) . '">' . $value . '</a>';
                        continue;
                    }

                    //ДАННЫЕ ВЫВОДЯТСЯ НАПРЯМУЮ БЕЗ ОБРАБОТКИ
                    $row[$field] = htmlspecialchars ( $value );
                }
            }

            //Страница контента (Принадлежит)
            if ( isset ( $options['nouns']['holder'] ) )
            {
                $contentRows = SqlTools::selectRows ( "SELECT * FROM `prefix_content` WHERE `id` = " . $i['holder'], MYSQLI_ASSOC );
                if ( $i['holder'] == 0 )
                {
                    $row['holder'] = '<span style="font-size:12px; color:#aaa;">не ограничено</span>';
                }
                else if ( count ( $contentRows ) > 0 )
                {
                    $holder = array_shift ( $contentRows );
                    $row['holder'] = '<span style="font-size:12px;">' . $holder['name'] . '</span>';
                }
                else
                {
                    $row['holder'] = '<span style="font-size:12px; color:red;">раздел не существует</span>';
                }
            }

            $rows[] = $row;

            if ( !isset ( $options["controls"]["editIf"] ) )
            {
                $edit = true;
            }
            else
            {
                foreach ( $options["controls"]["editIf"] as $rule )
                {
                    $edit = $i[$rule['field']] == $rule['value'];
                }
            }
            $rowsOpts[count ( $rows ) - 1]["edit"] = $edit;
        }

        //Подсветка измененного
        $highlight = 0;
        if ( isset ( $_SERVER['HTTP_REFERER'] ) )
        {
            $parsedref = array ();
            parse_str ( parse_url ( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY ), $parsedref );
            if ( isset ( $parsedref['edit_text'] ) )
            {
                $highlight = $parsedref['edit_text'];
            }
        }

        //Дополнительный GET параметр top
        $top = array ();
        if ( isset ( $_GET['top'] ) )
        {
            $top['top'] = $_GET['top'];
        }

        $datatable = tpl ( 'widgets/table', array (
            'thead' => @$header,
            'tbody' => $rows,
            'tRowsOpts' => $rowsOpts,
            'plink' => $this->GetLink ( '', $top ),
            'table' => $table,
            'options' => $options,
            'syscolumns' => $syscolumns,
            'highlight' => $highlight
            ) );

        //Форма добавления/редактирования jQueryUI
        $edit = $this->DataTable_AddEdit ( $_SERVER['REQUEST_URI'], $table, $options, $syscolumns, $fields );

        return $datatable . $edit;
    }

    /**
     *
     */
    protected function DataTable_AddEdit ( $link, $table, $options, $syscolumns = array (), $fields = array () )
    {
        if ( empty ( $syscolumns ) )
        {
            //Собираем данные о полях таблицы
            $syscolumns = $this->TableColumns ( $table );
        }

        //Отдаем JSON для формы редактирования
        if ( isset ( $_GET['getFieldsById'] ) )
        {
            $rowId = ( int ) $_GET['getFieldsById'];
            $form = SqlTools::selectRow ( "SELECT * FROM `prefix_$table` WHERE `id` = " . $rowId, MYSQL_ASSOC );
            $jsoni = array ();
            if ( $form && count ( $form ) )
            {
                foreach ( $form as $k => $v )
                {
                    if ( !empty ( $fields ) && !array_key_exists ( $k, $fields ) && (isset ( $options['nouns']['holder'] ) && $options['nouns']['holder'] != $k) )
                    {
                        continue;
                    }

                    //Мультиселект
                    if ( isset ( $fields[$k]['multiselect'] ) )
                    {
                        $v = @unserialize ( $v );
                    }

                    //autocomplete из таблицы autocomplete
                    $values = null;
                    if ( isset ( $fields[$k]['autocomplete'] ) && $fields[$k]['autocomplete'] == 'autocomplete_table' )
                    {
                        $values = $this->getAutocompleteValuesList ( $table, $k, $rowId );
                    }

                    $jsoni[] = array (
                        'name' => $k,
                        'value' => $v, //str_replace(array("\r\n",'"'),array('\r\n','\\"'),$v),
                        'type' => $syscolumns[$k]['Type'],
                        'values' => ($values) ? $values : "", //для заполнения autocomplete из таблицы autocomplete
                        'hide' => ( array_key_exists ( "hide", $fields[$k] ) ? $fields[$k]['hide'] : false )
                    );
                }
            }
            else
            {
                $top = filter_input ( INPUT_GET, "top", FILTER_SANITIZE_NUMBER_INT );
                foreach ( array_keys ( $syscolumns ) as $key )
                {
                    if ( $key == "id" )
                    {
                        $value = 0;
                    }
                    else if ( $key == "top" )
                    {
                        $value = $top;
                    }
                    elseif ( array_key_exists ( $key, $fields ) && array_key_exists ( "default", $fields[$key] ) )
                    {
                        $value = $fields[$key]['default'];
                    }
                    else
                    {
                        $value = "";
                    }
                    $jsoni[] = array
                    (
                        'name' => $key,
                        'value' => $value, //str_replace(array("\r\n",'"'),array('\r\n','\\"'),$v),
                        'type' => $syscolumns[$key]['Type'],
                        'values' => "" //для заполнения autocomplete из таблицы autocomplete
                    );
                }
            }

            //Изображение
            if ( isset ( $options['nouns']['image'] ) && $options['nouns']['image'] )
            {
                if ( is_file ( DOCROOT . '/data/' . $table . '/' . $f[0]['id'] . '/' . $f[0]['id'] ) )
                {
                    $jsoni[] = array
                    (
                        'name' => 'image',
                        'value' => '/data/' . $table . '/' . $f[0]['id'] . '/' . $f[0]['id'] . '?rnd=' . rand ( 1000, 20000 ),
                        'type' => 'Image'
                    );
                }
                else
                {
                    $jsoni[] = array
                    (
                        'name' => 'image',
                        'value' => '',
                        'type' => 'Image'
                    );
                }
            }

            //$json = json_encode( $jsoni );
            sendJSON ( $jsoni );
            //echo $json;
            exit ();
        }

        //Добавление записи
        if ( isset ( $_POST['id'] ) && $_POST['id'] == 0 )
        {
            $sql = "INSERT INTO `prefix_$table` "; // VALUES";

            $insert_fields = array ();
            foreach ( $_POST as $k => $v )
            {
                if ( $k == 'id' || !array_key_exists ( $k, $syscolumns ) )
                {
                    continue;
                }

                //Перевод и создание URI
                if ( isset ( $fields[$k]['if_empty_make_uri'] ) && empty ( $v ) )
                {
                    if ( isset ( $_POST[$fields[$k]['if_empty_make_uri']] ) )
                    {
                        $v = makeURI ( $_POST[$fields[$k]['if_empty_make_uri']] );
                    }
                }

                if ( array_key_exists ( "beforeInsert", $fields[$k] ) )
                {
                    $funcName = $fields[$k]['beforeInsert'];
                    $v = $funcName ( $v );
                }

                //Мультиселект
                if ( isset ( $fields[$k]['multiselect'] ) )
                {
                    $v = serialize ( $v );
                }

                if ( isset ( $fields[$k]['enum'] ) )
                {
                    $rowdata = $fields[$k]['enum'][$v];
                    $insert_fields[] = "`name`";
                    $insert_values[] = "'" . SqlTools::escapeString ( $rowdata['name'] ) . "'";
                }

                $insert_fields[] = "`$k`";
                $insert_values[] = "'" . SqlTools::escapeString ( $v ) . "'";
            }

            //Дата создания, подставляем автоматически, если такое поле есть
            if ( array_key_exists ( 'created', $options['nouns'] ) && in_array ( $options['nouns']['created'], array_keys ( $syscolumns ) ) )
            {
                $insert_fields[] = "`{$options['nouns']['created']}`";
                $insert_values[] = "NOW()";
            }

            $sql .= '(' . implode ( ',', $insert_fields ) . ') VALUES (' . implode ( ',', $insert_values ) . ')';

            $id = SqlTools::insert ( $sql );
            if ( isset ( $_FILES['image']['name'] ) )
            {
                $ext = strtolower ( substr ( $_FILES['image']['name'], -3 ) );
                if ( $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )
                {
                    /* раскомментировать если не будет работать оставшийся mkdir
                    if ( !is_dir ( DOCROOT . '/data' ) )
                    {
                        mkdir ( DOCROOT . '/data', 0777 );
                    }

                    if ( !is_dir ( DOCROOT . '/data/' . $table ) )
                    {
                        mkdir ( DOCROOT . '/data/' . $table, 0777 );
                    }
                    */
                    if ( !is_dir ( DOCROOT . '/data/' . $table . '/' . $id ) )
                    {
                        mkdir ( DOCROOT . '/data/' . $table . '/' . $id, 0777, true );
                    }

                    copy ( $_FILES['image']['tmp_name'], DOCROOT . '/data/' . $table . '/' . $id . '/' . $id );
                }
            }

            if ( array_key_exists ( 'HTTP_X_REQUESTED_WITH', $_SERVER ) && strtolower ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
            {
                if ( array_key_exists ( 'mode', $_POST ) && $_POST['mode'] == 'save' )
                {
                    sendJSON ( array ( "success" => 1, "id" => $id ) );
                    exit;
                }
                else
                {
                    sendJSON ( array ( "success" => 1, "redirect" => $link ) );
                }

                exit;
            }
            else
            {
                header ( "Location: " . $link );
            }
        }

        //Редактирование записи
        if ( isset ( $_POST['id'] ) && $_POST['id'] != 0 )
        {
            $sql = "UPDATE `prefix_$table` SET "; // VALUES";
            $insert_fields = array ();

            foreach ( $_POST as $k => $v )
            {
                if ( $k == 'id' || !array_key_exists ( $k, $syscolumns ) )
                {
                    continue;
                }

                //Перевод и создание URI
                if ( isset ( $fields[$k]['if_empty_make_uri'] ) && empty ( $v ) )
                {
                    if ( isset ( $_POST[$fields[$k]['if_empty_make_uri']] ) )
                    {
                        $v = makeURI ( $_POST[$fields[$k]['if_empty_make_uri']] );
                    }
                }

                if ( array_key_exists ( "beforeUpdate", $fields[$k] ) )
                {
                    $funcName = $fields[$k]['beforeUpdate'];
                    $v = $funcName ( $v );
                }

                //Мультиселект
                if ( isset ( $fields[$k]['multiselect'] ) )
                {
                    $v = serialize ( $v );
                }

                if ( isset ( $fields[$k]['enum'] ) )
                {
                    $rowdata = $fields[$k]['enum'][$v];
                    $insert_fields[] = "`name`";
                    $insert_values[] = "'" . SqlTools::escapeString ( $rowdata['name'] ) . "'";
                }

                $update_values[] = "`$k` = '" . SqlTools::escapeString ( $v ) . "'";
            }

            //Дата создания, подставляем автоматически, если такое поле есть
            if ( array_key_exists ( 'modified', $options['nouns'] ) && in_array ( $options['nouns']['modified'], array_keys ( $syscolumns ) ) )
            {
                $update_values[] = "`{$options['nouns']['modified']}` = NOW()";
            }

            $sql .= implode ( ',', $update_values ) . ' WHERE `id` = ' . ( int ) $_POST['id'];
            SqlTools::execute ( $sql );

            //print_r($_FILES);
            if ( isset ( $_FILES['image']['name'] ) )
            {
                $ext = strtolower ( substr ( $_FILES['image']['name'], -3 ) );
                if ( $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )
                {
                    if ( !is_dir ( DOCROOT . '/data/' . $table . '/' . $_POST['id'] ) )
                    {
                        mkdir ( DOCROOT . '/data/' . $table . '/' . $_POST['id'], 0777, true );
                    }
                    copy ( $_FILES['image']['tmp_name'], DOCROOT . '/data/' . $table . '/' . $_POST['id'] . '/' . $_POST['id'] );
                    //Удаляем старые отресайзеные файлы
                    if ( is_dir ( DOCROOT . '/data/thumbs/data/' . $table . '/' . $_POST['id'] ) )
                    {
                        $thumbs = scandir ( DOCROOT . '/data/thumbs/data/' . $table . '/' . $_POST['id'] );
                        foreach ( $thumbs as $thumb )
                        {
                            if ( is_file ( DOCROOT . '/data/thumbs/data/' . $table . '/' . $_POST['id'] . '/' . $thumb ) )
                            {
                                unlink ( DOCROOT . '/data/thumbs/data/' . $table . '/' . $_POST['id'] . '/' . $thumb );
                            }
                        }
                    }
                }
            }

            //Событие
            if ( method_exists ( $this, 'OnDataTableEdit' ) )
                $this->OnDataTableEdit ( $_POST );

            if ( array_key_exists ( 'HTTP_X_REQUESTED_WITH', $_SERVER ) && strtolower ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
            {
                if ( array_key_exists ( 'mode', $_POST ) && $_POST['mode'] == 'save' )
                {
                    sendJSON ( array ( "success" => 1, "id" => $_POST['id'] ) );
                    exit;
                }
                else
                {
                    sendJSON ( array ( "success" => 1, "redirect" => $link ) );
                }

                exit;
            }
            else
            {
                header ( "Location: " . $link );
            }
        }

        //Готовим поля для формы
        $we_have_files_fields = false;
        $addjs = array ();
        foreach ( $syscolumns as $k => $column )
        {
            if ( !empty ( $fields ) && !in_array ( $k, array_keys ( $fields ) ) && (!isset ( $options['nouns']['holder'] ) || $options['nouns']['holder'] != $k) )
            {
                continue;
            }

            if ( $column['Key'] == 'PRI' )
            {
                continue;
            }

            if ( isset ( $fields[$k]['default'] ) )
            {
                $default = $fields[$k]['default'];
            }
            elseif ( !empty ( $column['Default'] ) )
            {
                $default = $column['Default'];
            }
            else
            {
                $default = '';
            }

            // стиль для div с input и label (например, display:none)
            $formDivStyle = ( isset ( $fields[$k]['formDivStyle'] ) ) ? $fields[$k]['formDivStyle'] : "";
            // просто невидимость в форме div с input и label
            $formDivStyle = ( array_key_exists ( 'hide_from_form', $fields[$k] ) ? "display:none;" : "" ) . $formDivStyle;

            //КАСТОМНЫЕ ПОЛЯ
            //Автокомплит (jQ Autocomplete)
            if ( isset ( $fields[$k]['autocomplete'] ) )
            {
                //Если генерим автокомплит из этого же поля
                //т.е. с данными, которые уже были
                $vjsi = array ();
                if ( $fields[$k]['autocomplete'] == 'this' )
                {
                    $distincts = SqlTools::selectRows ( "SELECT DISTINCT `$k` FROM `prefix_$table` WHERE `$k` != ''", MYSQLI_ASSOC );
                    foreach ( $distincts as $i )
                    {
                        $vjsi[] = '"' . $i[$k] . '"';
                    }
                }
                elseif ( is_array ( $fields[$k]['autocomplete'] ) )
                {
                    foreach ( $fields[$k]['autocomplete'] as $i )
                    {
                        $vjsi[] = '"' . $i . '"';
                    }
                }

                $html[] = '
					<div class="form-group col-md-12" style="' . $formDivStyle . '">
						<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
						<input name="' . $k . '" id="se_' . $table . '_' . $k . '" def="' . $default . '" class="form-control text ui-widget-content ui-corner-all autocomplete" />
					</div>';
                $minLength = 0;
                if ( count ( $vjsi ) > 100 )
                {
                    $minLength = 1;
                }

                $vjs = '
					$("#se_' . $table . '_' . $k . '").autocomplete({
						minLength: ' . $minLength . ',
						source: [' . implode ( ',', $vjsi ) . ']
					}).click(function(){
						$("#se_' . $table . '_' . $k . '").autocomplete("search", "");
					});
				';
                $addjs[] = $vjs;

                continue;
            }

            //Селект (select)
            // ['select']['table'] - имя таблицы, из которой выбирать
            // ['select']['name'] - поле (или массив из массива 'fields' - поля
            // и элемента 'delim' - разделитель между значениями для отображения),
            // которое(ые) будет(ут) отображаться в списке формы
            // ['select']['id'] - поле ИД - первичного ключа
            // ['select']['where'] - where выражение для отбора из другой таблицы, не начинается с AND
            // ['select']['addField'] - дополнительное, кроме name, поле для отбора из другой таблицы
            if ( isset ( $fields[$k]['multiselect'] ) )
            {
                $fields[$k]['select'] = $fields[$k]['multiselect'];
            }

            if ( isset ( $fields[$k]['select'] ) && isset ( $fields[$k]['select']['table'] ) && isset ( $fields[$k]['select']['name'] ) )
            {
                if ( isset ( $fields[$k]['select']['id'] ) )
                {
                    $id_field = ', `' . $fields[$k]['select']['id'] . '`';
                }
                else
                {
                    $id_field = '';
                }

                if ( isset ( $fields[$k]['select']['order'] ) )
                {
                    $order_field = 'ORDER BY `' . $fields[$k]['select']['order'] . '`';
                }
                else
                {
                    $order_field = '';
                }

                if ( isset ( $fields[$k]['select']['deleted'] ) )
                {
                    $is_deleted = "AND `{$fields[$k]['select']['deleted']}` = 'N'";
                }
                else
                {
                    $is_deleted = '';
                }

                if ( isset ( $fields[$k]['select']['where'] ) )
                {
                    $where_field = $fields[$k]['select']['where'];
                }
                else
                {
                    $where_field = '';
                }

                if ( isset ( $fields[$k]['select']['addField'] ) )
                {
                    $addField = ', `' . $fields[$k]['select']['addField'] . '`';
                }
                else
                {
                    $addField = '';
                }

                //Если данные нужно построить деревом
                if ( isset ( $fields[$k]['select']['top'] ) )
                {
                    if ( isset ( $fields[$k]['select']['allow_null'] ) && $fields[$k]['select']['allow_null'] )
                    {
                        $opts = '<option value=""></option>';
                    }
                    else
                    {
                        $opts = '';
                    }

                    //Рекурсивно собираем категории
                    $make_select_tree = function ( $tid, $field, $default, $c = 0, $order_field, $is_deleted, $where_field, $make_select_tree )
                    {
                        global $addField;
                        // если в select отбирать более одного поля, т.е., ['select']['name'] - массив
                        $fieldNames = array ();
                        if ( is_array ( $field['select']['name'] ) )
                        {
                            $fieldNames = $field['select']['name']['fields'];
                        }
                        else
                        {
                            $fieldNames[] = $field['select']['name'];
                        }

                        if ( count ( $fieldNames ) > 1 )
                        {
                            $arg = '';
                            $delim = ", '{$field['select']['name']['delim']}', ";
                            foreach ( $fieldNames as $fieldName )
                            {
                                $arg = $arg . "`" . $fieldName . "`" . $delim;
                            }
                            $arg = substr ( $arg, 0, strlen ( $arg ) - strlen ( $delim ) );
                            $concat = " CONCAT($arg) ";
                        }
                        else
                        {
                            $concat = " `{$fieldNames[0]}` ";
                        }

                        $field['select']['name'] = $fieldNames[0];

                        $fields = SqlTools::selectRows ( "
							SELECT $concat AS {$field['select']['name']}, `{$field['select']['id']}` $addField
							FROM `prefix_{$field['select']['table']}`
							WHERE `{$field['select']['top']}` = $tid
							$where_field
							$is_deleted
							$order_field", MYSQL_ASSOC );
                        $opts = '';
                        foreach ( $fields as $i )
                        {
                            $value = $i[$field['select']['id']];

                            if ( $default == $value )
                            {
                                $selected = "selected"; //="selected"';
                            }
                            else
                            {
                                $selected = '';
                            }

                            if ( $c > 0 )
                            {
                                $prefix = str_repeat ( '-', $c ) . ' ';
                            }
                            else
                            {
                                $prefix = '';
                            }
                            // добавляем, если есть, доп поле и его значение как атрибуты
                            $attrAddField = !empty ( $field['select']['addField'] ) ? $field['select']['addField'] . '="' . $i[$field['select']['addField']] . '" ' : '';
                            $opts .= '<option value="' . $value . '" ' . $selected . $attrAddField . '>' . $prefix
                                . htmlspecialchars ( $i[$field['select']['name']] ) . '</option>';
                            $opts .= $make_select_tree ( $i[$field['select']['id']], $field, $default, $c + 1, $order_field, $is_deleted, $where_field, $make_select_tree );
                        }
                        return $opts;
                    };
                    $opts = $make_select_tree ( 0, $fields[$k], $default, 0, $order_field, $is_deleted, $where_field, $make_select_tree );
                }
                //Данные простым списком
                else
                {
                    $fieldNames = array ();
                    if ( is_array ( $fields[$k]['select']['name'] ) )
                    {
                        $fieldNames = $fields[$k]['select']['name']['fields'];
                    }
                    else
                    {
                        $fieldNames[] = $fields[$k]['select']['name'];
                    }

                    if ( count ( $fieldNames ) > 1 )
                    {
                        $arg = '';
                        $delim = ", '{$fields[$k]['select']['name']['delim']}', ";
                        foreach ( $fieldNames as $fieldName )
                        {
                            $arg = $arg . "`" . $fieldName . "`" . $delim;
                        }
                        $concat = substr ( $arg, 0, strlen ( $arg ) - strlen ( $delim ) );
                        $fields[$k]['select']['name'] = $fieldNames[0];
                        $query = "SELECT CONCAT($concat) AS {$fields[$k]['select']['name']} $id_field $addField "
                            . " FROM `prefix_{$fields[$k]['select']['table']}` "
                            . " WHERE 1 $where_field $is_deleted $order_field";
                        $datalist = SqlTools::selectRows ( $query );
                    }
                    else
                    {
                        $fields[$k]['select']['name'] = $fieldNames[0];
                        $datalist = SqlTools::selectRows ( "SELECT `{$fields[$k]['select']['name']}` $id_field FROM `prefix_{$fields[$k]['select']['table']}` WHERE 1 $where_field $is_deleted $order_field" );
                    }


                    if ( isset ( $fields[$k]['select']['allow_null'] ) && $fields[$k]['select']['allow_null'] )
                    {
                        $opts = '<option value=""></option>';
                    }
                    else
                    {
                        $opts = '';
                    }

                    foreach ( $datalist as $i )
                    {
                        if ( isset ( $fields[$k]['select']['id'] ) )
                        {
                            $value = $i[$fields[$k]['select']['id']];
                        }
                        else
                        {
                            $value = $i[$fields[$k]['select']['name']];
                        }

                        if ( $default == $value )
                        {
                            $selected = "selected"; //="selected"';
                        }
                        else
                        {
                            $selected = '';
                        }
                        // добавляем, если есть, доп поле и его значение как атрибуты
                        $attrAddField = !empty ( $fields[$k]['select']['addField'] ) ? $fields[$k]['select']['addField'] . '="' . $i[$fields[$k]['select']['addField']] . '" ' : '';

                        $opts .= '<option value="' . $value . '" ' . $selected . $attrAddField . '>' . htmlspecialchars ( $i[$fields[$k]['select']['name']] ) . '</option>';
                    }
                }

                //Если у нас мультиселект
                if ( isset ( $fields[$k]['multiselect'] ) )
                {
                    if ( isset ( $fields[$k]['select']['size'] ) )
                    {
                        $multi_size_val = $fields[$k]['select']['size'];
                    }
                    else
                    {
                        $multi_size_val = 4;
                    }

                    $multi = 'multiple="multiple"';
                    $multi_array = '[]';
                    $multi_size = 'size="' . $multi_size_val . '"';
                }
                else
                {
                    $multi = '';
                    $multi_array = '';
                    $multi_size = '';
                }

                $html[] = '
					<div class="form-group col-md-4" style="' . $formDivStyle . '">
						<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
						<select name="' . $k . $multi_array . '" id="se_' . $table . '_' . $k . '" class="form-control text ui-widget-content ui-corner-all" ' . $multi_size . ' ' . $multi . '>
							' . $opts . '
						</select>
					</div>';

                continue;
            }

            // Текстовый редактор для поля
            if ( isset ( $fields[$k]['edit_text'] ) )
            {

                $html[] = '<div class="form-group col-md-12" style="' . $formDivStyle . '">'
                    . '<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>'
                    . '<textarea class="form-control edit_text text ui-widget-content ui-corner-all tinymce" name="' . $k . '" id="se_' . $table . '_' . $k . '"></textarea>'
                    . '</div>'
                    . '<br>';

                $vjs = '';
                if ( isset ( $fields[$k]['length'] ) && !empty ( $fields[$k]['length'] ) )
                {
                    $vjs .= 'bValid = bValid && checkLength($("#se_' . $table . '_' . $k . '"),"'
                        . $fields[$k]['name'] . '",' . str_replace ( '-', ',', $fields[$k]['length'] ) . ');';
                }
                if ( isset ( $fields[$k]['regex'] ) && !empty ( $fields[$k]['regex'] ) )
                {
                    $vjs .= 'bValid = bValid && checkRegexp($("#se_' . $table . '_' . $k . '"),'
                        . $fields[$k]['regex'] . ',"' . $fields[$k]['regex_error'] . '");';
                }

                $js[] = $vjs;

                continue;
            }

            if ( isset ( $fields[$k]['enum'] ) )
            {
                $opts = "";
                foreach ( $fields[$k]['enum'] as $rec )
                {
                    $opts .= "<option value='" . $rec['type'] . "'>" . $rec['name'] . "</option>";
                }
                $multi = '';
                $multi_array = '';
                $multi_size = '';
                $html[] = '
					<div class="floating_fields" style="' . $formDivStyle . '">
						<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
						<select name="' . $k . $multi_array . '" id="se_' . $table . '_' . $k . '" class="text ui-widget-content ui-corner-all" ' . $multi_size . ' ' . $multi . '>
							' . $opts . '
						</select>
					</div>';
                continue;
            }
            //АВТООПРЕДЕЛЕНИЕ ПОЛЕЙ
            switch ( $column['Type'] )
            {

                //Текст (textarea)
                case 'text':
                    $html[] = '
					<div class="form-group col-md-12" style="' . $formDivStyle . '">
						<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
						<textarea name="' . $k . '" id="se_' . $table . '_' . $k . '" class="form-control text ui-widget-content ui-corner-all tinymce" def="' . $default . '"></textarea>
					</div>';

                    $vjs = '';
                    if ( isset ( $fields[$k]['length'] ) && !empty ( $fields[$k]['length'] ) )
                    {
                        $vjs .= 'bValid = bValid && checkLength($("#se_' . $table . '_' . $k . '"),"' . $fields[$k]['name'] . '",' . str_replace ( '-', ',', $fields[$k]['length'] ) . ');';
                    }
                    if ( isset ( $fields[$k]['regex'] ) && !empty ( $fields[$k]['regex'] ) )
                    {
                        $vjs .= 'bValid = bValid && checkRegexp($("#se_' . $table . '_' . $k . '"),' . $fields[$k]['regex'] . ',"' . $fields[$k]['regex_error'] . '");';
                    }

                    $js[] = $vjs;
                    break;

                //Да — нет
                case "enum('Y','N')":
                    $default = array_key_exists ( "default", $fields[$k] ) && $fields[$k]['default'] ? $fields[$k]['default'] : $column['Default'];
                    $html[] = '
                        <div class="form-group col-md-12" style="' . $formDivStyle . '">
                                <label>' . $fields[$k]['name'] . '</label>
                                <input type="radio" name="' . $k . '" value="Y" id="se_' . $table . '_' . $k . '_y" class="radio" ' . ($default == 'Y' ? 'checked="checked"' : '') . ' /> <label for="se_' . $table . '_' . $k . '_y" style="display:inline">Да</label>
                                <input type="radio" name="' . $k . '" value="N" id="se_' . $table . '_' . $k . '_n" class="radio" ' . ($default == 'N' ? 'checked="checked"' : '') . ' /> <label for="se_' . $table . '_' . $k . '_n" style="display:inline">Нет</label>
                        </div>';

                    $vjs = '';
                    $js[] = $vjs;

                    break;

                case "enum('ENUM','INTERVAL','SET')":
                    $html[] = '
                        <div class="fullwidth_fields" style="' . $formDivStyle . '">
                                <label>' . $fields[$k]['name'] . '</label>
                                <input type="radio" name="' . $k . '" value="ENUM" id="se_' . $table . '_' . $k . '_ENUM" class="radio" ' . ($column['Default'] == 'ENUM' ? 'checked="checked" def="1"' : '') . ' />'
                        . '<label for="se_' . $table . '_' . $k . '_ENUM" style="display:inline">Есть/Нет</label>
                                <input type="radio" name="' . $k . '" value="INTERVAL" id="se_' . $table . '_' . $k . '_INTERVAL" class="radio" ' . ($column['Default'] == 'INTERVAL' ? 'checked="checked" def="1"' : '') . ' />'
                        . '<label for="se_' . $table . '_' . $k . '_INTERVAL" style="display:inline">Число</label>
                                <input type="radio" name="' . $k . '" value="SET" id="se_' . $table . '_' . $k . '_SET" class="radio" ' . ($column['Default'] == 'SET' ? 'checked="checked" def="1"' : '') . ' /> '
                        . '<label for="se_' . $table . '_' . $k . '_SET" style="display:inline">Текст (перечень)</label>
                        </div>';

                    $vjs = '';
                    $js[] = $vjs;

                    break;

                //Календарик с датами
                case 'datetime': case 'date':
                    $html[] = '
					<div class="form-group col-md-3" style="' . $formDivStyle . '">
						<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
						<input name="' . $k . '" id="se_' . $table . '_' . $k . '" class="form-control text ui-widget-content ui-corner-all datefield" def="' . $default . '" />
					</div>';

                    $vjs = '';
                    $vjs .= 'bValid = bValid && checkRegexp($("#se_' . $table . '_' . $k . '"),/(19|20)[0-9]{2}[\- \/.](0[1-9]|1[012])[\- \/.](0[1-9]|[12][0-9]|3[01])/im,"Не верно заполнено поле с датой");';

                    $js[] = $vjs;
                    break;


                default:
                    //Страница контента (Принадлежит) (SELECT)
                    if ( isset ( $options['nouns']['holder'] ) && $column['Field'] == $options['nouns']['holder'] )
                    {
                        if ( isset ( $options['nouns']['holder_module'] ) )
                        {
                            $module_name = $options['nouns']['holder_module'];
                        }
                        else
                        {
                            $array_debug = debug_backtrace ();
                            $module_name = $array_debug[0]['class'];
                        }

                        $contentRows = SqlTools::selectRows ( "SELECT * FROM `prefix_content` WHERE `module` = '$module_name'", MYSQL_ASSOC );
                        $holder_select = '
						<div class="fullwidth_fields" style="' . $formDivStyle . '">
							<label for="se_' . $table . '_' . $k . '">Запись принадлежит разделу</label>
							<select name="' . $k . '" id="se_' . $table . '_' . $k . '" style="margin-bottom:12px">
								<option value="0">не ограничено разделом</option>';
                        foreach ( $contentRows as $i )
                        {
                            $holder_select .= '<option value="' . $i['id'] . '">' . $i['name'] . '</option>';
                        }
                        $holder_select .= '</select>
						</div>';

                        $html[] = $holder_select;

                        $vjs = '';
                        $js[] = $vjs;
                    }
                    //ENUM(...)
                    else if ( strpos ( $column['Type'], 'enum(' ) === 0 )
                    {
                        $radio = explode ( "','", substr ( $column['Type'], 6, -2 ) );

                        $enum_radio = '<label>' . $fields[$k]['name'] . '</label><div id="se_' . $table . '_' . $k . '">';
                        foreach ( $radio as $i )
                        {
                            $radio_name = $i == '' ? 'Не указано' : $i;
                            $enum_radio .= '<input type="radio" name="' . $k . '" value="' . $i . '" id="se_' . $table . '_' . $k . '_' . $i . '" class="radio" ' . ($default == $i ? 'checked="checked" def="1"' : '') . ' /> <label for="se_' . $table . '_' . $k . '_' . $i . '" style="display:inline">' . $radio_name . '</label>';
                        }


                        $html[] = '
						<div class="fullwidth_fields" style="' . $formDivStyle . '">
							' . $enum_radio . '
						</div>';

                        $vjs = '';
                        $js[] = $vjs;
                    }
                    //Текстовое поле (input)
                    else
                    {
                        $html[] = '
						<div class="form-group col-md-4" style="' . $formDivStyle . '">
							<label for="se_' . $table . '_' . $k . '">' . $fields[$k]['name'] . '</label>
							<input name="' . $k . '" id="se_' . $table . '_' . $k . '" class="form-control text ui-widget-content ui-corner-all" def="' . $default . '" />
						</div>';
                        $vjs = '';
                        if ( isset ( $fields[$k]['length'] ) && !empty ( $fields[$k]['length'] ) )
                        {
                            $vjs .= 'bValid = bValid && checkLength($("#se_' . $table . '_' . $k . '"),"' . $fields[$k]['name'] . '",' . str_replace ( '-', ',', $fields[$k]['length'] ) . ');';
                        }

                        if ( isset ( $fields[$k]['regex'] ) && !empty ( $fields[$k]['regex'] ) )
                        {
                            $vjs .= 'bValid = bValid && checkRegexp($("#se_' . $table . '_' . $k . '"),' . $fields[$k]['regex'] . ',"' . $fields[$k]['regex_error'] . '");';
                        }

                        $js[] = $vjs;
                    }
                    break;
            }
        }

        //Картинка
        if ( isset ( $options['nouns']['image'] ) && $options['nouns']['image'] )
        {
            //if(is_file(DOCROOT.'/data/'.$table.'/'.$_POST['id'].'/'.$_POST['id'].'.jpg'))
            $html[] = '
			<div class="fullwidth_fields" style="">
				<label for="se_' . $table . '_image">Изображение (Максимальный размер ' . get_max_filesize () . ')</label>
				<input type="file" name="image" id="se_' . $table . '_image" class="text ui-widget-content ui-corner-all" />
				<div id="se_' . $table . '_image_prev"></div>
			</div>
				';
            $we_have_files_fields = true;
        }

        //Табы
        $tabs = array ();
        if ( isset ( $options['tabs'] ) )
        {
            foreach ( $options['tabs'] as $k => $v )
            {
                if ( !method_exists ( $this, $k ) )
                {
                    continue;
                }
                $tabs[$k] = array
                (
                    'method' => $k,
                    'name' => $v
                );
            }
        }

        $form = tpl ( 'widgets/simple_edit', array
        (
            'plink' => $link,
            'table' => $table,
            'js' => implode ( "\r\n", $js ),
            'html' => $html,
            'we_have_files_fields' => $we_have_files_fields,
            'tabs' => $tabs,
            'addjs' => $addjs
        ) );

        return $form;
    }

    /**
     * <pre>Оперирует с данными из таблиц базы данных (таблицы компонентов)</pre>
     * @param String $table <p>Имя таблицы (компонента)</p>
     * @param Array $options <p>Опции действий</p>
     * @param Array $fields <p>Список полей таблицы для операции</p>
     * @param String $where <p>Селекторы для SQL-запроса</p>
     * @param String $order <p>Поле и порядок сортировки</p>
     * @return String <p>Строка с таблицами</p>
     */
    function DataTableAdvanced ( $table, $options = array (), $fields = array (), $where = '', $order = '' )
    {
        //Собираем данные о полях таблицы
        $syscolumns = $this->TableColumns ( $table );
        $deleted_field_exist = false;
        if ( isset ( $options['nouns']['deleted'] ) )
        {
            foreach ( $syscolumns as $column )
            {
                if ( $column['Field'] == $options['nouns']['deleted'] )
                {
                    $deleted_field_exist = true;
                }
            }
        }

        //Удаление
        if ( isset ( $_GET['delete'] ) )
        {
            if ( $deleted_field_exist )
            {
                SqlTools::execute ( "UPDATE `prefix_$table` SET `" . $options['nouns']['deleted'] . "` = 'Y' WHERE `" . $options['nouns']['id'] . "` = " . ( int ) $_GET['delete'] );
            }
            else
            {
                SqlTools::execute ( "DELETE FROM `prefix_$table` WHERE `" . $options['nouns']['id'] . "` = " . ( int ) $_GET['delete'] );
            }

            //Удаление записей подопечной таблицы
            if ( isset ( $options['inner']['table'] ) )
            {
                if ( isset ( $options['inner']['deleted'] ) )
                {
                    SqlTools::execute ( "UPDATE `prefix_{$options['inner']['table']}` SET `{$options['inner']['deleted']}` = 'Y' WHERE "
                        . " `{$options['inner']['top_key']}` IN (" . $_GET['delete'] . ")" );
                }
                else
                {
                    SqlTools::execute ( "DELETE FROM `prefix_{$options['inner']['table']}` "
                        . " WHERE `{$options['inner']['top_key']}` IN (" . $_GET['delete'] . ")" );
                }
            }

            //header('Location: '.$this->GetLink());
            //Ссылка для возврата назад, похоже самое простое решение это убрать delete из запроса
            $plink = preg_replace ( '/&?delete=[\d]+/i', '', $_SERVER['REQUEST_URI'] );
            header ( 'Location: ' . $plink );
        }

        //Сохранение сортировки
        if ( isset ( $_POST['tr' . $table] ) && is_array ( $_POST['tr' . $table] ) )
        {
            foreach ( $_POST['tr' . $table] as $k => $v )
            {
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['order']}` = " . (( int ) $k) . "  WHERE `{$options['nouns']['id']}` = " . ( int ) $v );
            }
            exit ();
        }

        //Редактирование данных из таблицы (аякс)
        if ( isset ( $_GET['update'] ) )
        {
            SqlTools::execute ( "UPDATE `prefix_" . SqlTools::escapeString ( $_POST['table'] ) . "` SET `" . SqlTools::escapeString ( $_POST['field'] ) . "` = '" . SqlTools::escapeString ( $_POST['val'] ) . "' WHERE `{$options['nouns']['id']}` = " . ( int ) $_POST['id'] );
            exit ();
        }

        //Редактирование текстового содержания
        if ( isset ( $_GET['edit_text'] ) )
        {

            //Сохранение
            if ( isset ( $_POST['text'] ) )
            {
                $newtext = $_POST['text']; //str_replace("'","\\'",$_POST['text']);
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['text']}` = '" . SqlTools::escapeString ( $newtext ) . "' WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['edit_text'] );
            }

            $i = SqlTools::selectRows ( "SELECT `{$options['nouns']['id']}`,`{$options['nouns']['name']}`,`{$options['nouns']['text']}` FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = " . ( int ) $_GET['edit_text'] );
            $text = $i[0][$options['nouns']['text']];
            $name = $i[0][$options['nouns']['name']];
            $id = $i[0][$options['nouns']['id']];

            //Ссылка для возврата назад, похоже самое простое решение это убрать edit_text из запроса
            $plink = preg_replace ( '/&?edit_text=[\d]+/i', '', $_SERVER['REQUEST_URI'] );

            $ret = tpl ( 'widgets/table_edit_text', array (
                'plink' => $plink,
                'text' => $text,
                'name' => $name,
                'id' => $id
                ) );

            $this->title = 'Редактирование «' . $name . '»';

            $this->hint = array
            (
                'title' => 'О редактировании текстов',
                'text' => '<p>Вы можете использовать блоки в формате <code>{block:0}</code> их можно добавить в разделе <a href="' . $this->GetLink ( 'Info', array (), 'Blocks' ) . '">блоков</a></p>'
            );

            return $ret;
        }

        if ( empty ( $where ) )
        {
            $where = '1';
        }

        if ( !empty ( $fields ) )
        {

        }

        if ( isset ( $_GET['order'] ) )
        {
            $order = SqlTools::escapeString ( $_GET['order'] );
        }

        if ( !isset ( $_GET['order'] ) && isset ( $options['nouns']['order'] ) && in_array ( $options['nouns']['order'], array_keys ( $syscolumns ) ) && in_array ( $options['nouns']['order'], array_keys ( $fields ) ) )
        {
            $order = $options['nouns']['order'];
        }

        if ( empty ( $order ) )
        {
            $order = $options['nouns']['id'];
        }

        if ( isset ( $_GET['orderd'] ) )
        {
            $orderd = $_GET['orderd'];
        }
        else
        {
            $orderd = 'ASC';
        }

        if ( $deleted_field_exist )
        {
            $where .= " AND `{$options['nouns']['deleted']}` = 'N'";
        }

        $tableRows = SqlTools::selectRows ( "SELECT * FROM `prefix_$table` WHERE $where ORDER BY `$order` $orderd", MYSQL_ASSOC );

        $rows = array ();
        /** опции для управления отображением записей в таблице */
        $rowsOpts = array ();
        $header = array ();
        foreach ( $tableRows as $i )
        {
            if ( !count ( $header ) )
            {

                foreach ( $i as $recKey => $v )
                {
                    if ( empty ( $fields ) )
                    {
                        $header[$recKey] = array (
                            'field' => $recKey,
                            'name' => $recKey
                        );
                    }
                    else
                    {
                        if ( array_key_exists ( $recKey, $fields ) && !isset ( $fields[$recKey]['hide_from_table'] ) )
                        {
                            $header[$recKey] = array (
                                'field' => $recKey,
                                'name' => $fields[$recKey]['name'],
                                'class' => isset ( $fields[$recKey]['class'] ) ? $fields[$recKey]['class'] : '',
                                'shortLabel' => isset ( $fields[$recKey]['shortLabel'] ) ? $fields[$recKey]['shortLabel'] : ''
                            );
                        }
                    }
                    if ( $order == $recKey )
                    {
                        $header[$recKey] = array_merge ( $header[$recKey], array ( 'order' => true, 'orderd' => $orderd ) );
                    }

                    if ( isset ( $fields[$recKey]['style'] ) )
                    {
                        $header[$recKey]['style'] = $fields[$recKey]['style'];
                    }
                }
                //Страница контента (Принадлежит)
                if ( isset ( $options['nouns']['holder'] ) )
                {
                    $header[$options['nouns']['holder']] = array (
                        'field' => $options['nouns']['holder'],
                        'name' => 'Принадлежит',
                        'class' => ''
                    );
                }
            }

            $row = array ();
            $select_replace_text = array ();
            foreach ( $i as $field => $value )
            {
                if ( (array_key_exists ( $field, $fields ) && !isset ( $fields[$field]['hide_from_table'] )) || empty ( $fields ) )
                {
                    //МОДИФИКАЦИЯ ДАННЫХ В ТАБЛИЦЕ
                    //Модификация через пользовательскую функцию (transform)
                    if ( isset ( $fields[$field]['transform'] ) )
                    {
                        if ( is_object ( $fields[$field]['transform'] ) )
                        {
                            $row[$field] = $fields[$field]['transform'] ( $value, $i );
                            continue;
                        }
                        elseif ( method_exists ( $this, $fields[$field]['transform'] ) )
                        {
                            $row[$field] = $this->$fields[$field]['transform'] ( $value, $i );
                            continue;
                        }
                    }

                    if ( isset ( $fields[$field]['enum'] ) )
                    {
                        $enumData = $fields[$field]['enum'];
                        $selecredData = $enumData[$i['type']];
                        $row[$field] = $selecredData['name'];
                        continue;
                    }
                    //Селект
                    // если поле заявлено как селект (select) - нужно его значения выбирать из другой таблицы
                    // ['select']['table'] - имя таблицы, из которой выбирать
                    // ['select']['name'] - поле (или массив из массива 'fields' - поля
                    // и элемента 'delim' - разделитель между значениями для отображения),
                    // которое(ые) будет(ут) отображаться в списке формы
                    // ['select']['id'] - поле ИД - первичного ключа
                    if ( isset ( $fields[$field]['multiselect'] ) )
                    {
                        $fields[$field]['select'] = $fields[$field]['multiselect'];
                    }
                    if ( isset ( $fields[$field]['select'] ) && isset ( $fields[$field]['select']['table'] ) && isset ( $fields[$field]['select']['name'] ) )
                    {
                        if ( !count ( $select_replace_text ) && !isset ( $select_replace_text[$fields[$field]['select']['table']] ) )
                        {
                            if ( isset ( $fields[$field]['select']['deleted'] ) )
                            { // признак не удалённых - наличие элемента deleted, его значение - имя поля признака удалённости
                                $is_deleted = "AND `{$fields[$field]['select']['deleted']}` = 'N'";
                            }
                            else
                            {
                                $is_deleted = '';
                            }
                            // выбираются все не deleted
                            $selectsRow = SqlTools::selectRows ( "SELECT * FROM `prefix_{$fields[$field]['select']['table']}` "
                                    . " WHERE 1 $is_deleted", MYSQL_ASSOC );

                            foreach ( $selectsRow as $si )
                            {
                                // $skey - значение идентифицирующего поля таблицы (первичного ключа или наименования)
                                if ( isset ( $fields[$field]['select']['id'] ) )
                                {
                                    $skey = $si[$fields[$field]['select']['id']];
                                }
                                else
                                {
                                    if ( is_array ( $fields[$field]['select']['name'] ) )
                                    {
                                        $skey = $si[implode ( ", ", $fields[$field]['select']['name']['fields'] )];
                                    }
                                    else
                                    {
                                        $skey = $si[$fields[$field]['select']['name']];
                                    }
                                }

                                // если несколько полей (массив), наименования выбираем их слепленные значения и помещаем в $select_replace_text
                                // (похоже, считается, что в этом случае идент-щим полем может быть только id)
                                $fieldNames = array ();
                                if ( is_array ( $fields[$field]['select']['name'] ) )
                                {
                                    $fieldNames = $fields[$field]['select']['name']['fields'];
                                }
                                else
                                {
                                    $fieldNames[] = $fields[$field]['select']['name'];
                                }

                                if ( count ( $fieldNames ) > 1 )
                                {
                                    $arg = "";
                                    $fieldNames = $fields[$field]['select']['name']['fields'];
                                    $delim = ", '{$fields[$field]['select']['name']['delim']}', ";
                                    foreach ( $fieldNames as $fieldName )
                                    {
                                        $arg = $arg . "`" . $fieldName . "`" . $delim;
                                    }
                                    $concat = substr ( $arg, 0, strlen ( $arg ) - strlen ( $delim ) );

                                    $query = "SELECT CONCAT($concat) AS {$fieldNames[0]} FROM `prefix_{$fields[$field]['select']['table']}` "
                                        . " WHERE `id`= {$skey}";

                                    $datalist = SqlTools::selectRow ( $query );

                                    $select_replace_text[$fields[$field]['select']['table']] [$skey] = $datalist[$fieldNames[0]];
                                }
                                else
                                {
                                    $select_replace_text[$fields[$field]['select']['table']] [$skey] = $si[$fieldNames[0]];
                                }
                            }
                        }
                        // присваиваем в массив записей для отображения
                        if ( isset ( $select_replace_text[$fields[$field]['select']['table']] [$value] ) )
                        {
                            $row[$field] = $select_replace_text[$fields[$field]['select']['table']] [$value];
                        }
                        else
                        {
                            $row[$field] = $value;
                        }

                        //Мультиселект
                        if ( isset ( $fields[$field]['multiselect'] ) )
                        {
                            $curr_values = @unserialize ( $value );
                            if ( is_array ( $curr_values ) )
                            {
                                $row_vals = array ();
                                foreach ( $curr_values as $mvalue )
                                {
                                    $row_vals[] = $select_replace_text[$fields[$field]['select']['table']] [$mvalue];
                                }
                                $row[$field] = implode ( ', ', $row_vals );
                            }
                        }

                        continue;
                    }

                    //Ссылка
                    if ( isset ( $fields[$field]['link'] ) )
                    {
                        $matches = array ();
                        preg_match ( '/\{(.+)\}/', $fields[$field]['link'], $matches );
                        $linkField = $matches[1];

                        $row[$field] = '<a href="' . str_replace ( "{" . $linkField . "}", $i[$options['nouns'][$linkField]], $fields[$field]['link'] ) . '">' . $value . '</a>';
                        continue;
                    }

                    //ДАННЫЕ ВЫВОДЯТСЯ НАПРЯМУЮ БЕЗ ОБРАБОТКИ
                    $row[$field] = htmlspecialchars ( $value );
                }
            }

            //Страница контента (Принадлежит)
            if ( isset ( $options['nouns']['holder'] ) )
            {
                $holders = SqlTools::selectRows ( "SELECT * FROM `prefix_content` WHERE `id` = " . $i['holder'], MYSQL_ASSOC );
                if ( $i['holder'] == 0 )
                {
                    $row['holder'] = '<span style="font-size:12px; color:#aaa;">не ограничено</span>';
                }
                else if ( count ( $holders ) > 0 )
                {
                    $holder = array_shift ( $holders );
                    $row['holder'] = '<span style="font-size:12px;">' . $holder['name'] . '</span>';
                }
                else
                {
                    $row['holder'] = '<span style="font-size:12px; color:red;">раздел не существует</span>';
                }
            }

            $rows[] = $row;
            if ( !isset ( $options["controls"]["editIf"] ) )
            {
                $edit = true;
            }
            else
            {
                foreach ( $options["controls"]["editIf"] as $rule )
                {
                    $edit = $i[$rule['field']] == $rule['value'];
                }
            }
            $rowsOpts[count ( $rows ) - 1]["edit"] = $edit;
        }

        //Подсветка измененного
        $highlight = 0;
        if ( isset ( $_SERVER['HTTP_REFERER'] ) )
        {
            $parsedref = array ();
            parse_str ( parse_url ( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY ), $parsedref );
            if ( isset ( $parsedref['edit_text'] ) )
            {
                $highlight = $parsedref['edit_text'];
            }
        }

        //Дополнительный GET параметр top
        $top = array ();
        if ( isset ( $_GET['top'] ) )
        {
            $top['top'] = $_GET['top'];
        }

        $datatable = tpl ( 'widgets/table_advanced', array
        (
            'thead' => @$header,
            'tbody' => $rows,
            'tRowsOpts' => $rowsOpts,
            'plink' => $this->GetLink ( '', $top ),
            'table' => $table,
            'options' => $options,
            'syscolumns' => $syscolumns,
            'highlight' => $highlight
            ) );

        //Форма добавления/редактирования jQueryUI
        $edit = $this->DataTable_AddEdit ( $_SERVER['REQUEST_URI'], $table, $options, $syscolumns, $fields );

        return $datatable . $edit;
    }

    function FileEdit ( $file, $form_action = '' )
    {

        if ( !is_file ( $file ) )
        {
            $ofile = $file;
            $file = DOCROOT . '/' . $file;
            if ( !is_file ( $file ) )
            {
                $this->title = 'Не найден файл «' . $ofile . '»';
                return;
            }
            $file = realpath ( $file );
            if ( !is_readable ( $file ) )
            {
                $this->title = 'Невозможно прочитать файл «' . $ofile . '»';
                return;
            }
        }

        //Сохранение
        if ( isset ( $_POST['text'] ) )
        {
            $newtext = $_POST['text'];
            if ( !is_writable ( $file ) )
            {
                $this->title = 'Невозможно записать в файл «' . $ofile . '»';
                return;
            }
            else
            {
                file_put_contents ( $file, $newtext );
            }
        }

        //Заголовок и хинт
        $this->title = 'Редактирование файла «' . $file . '»';
        $this->hint = array (
            'title' => 'О редактировании файлов',
            'text' => '
				<p>Подсветка синтаксиса предоставлена <a href="http://marijn.haverbeke.nl/codemirror/" target="_blank">CodeMirror</a></p>'
        );

        //Открываем файл
        $pathinfo = pathinfo ( $file ); // dirname basename extension filename
        $text = file_get_contents ( $file );
        $writable = is_writable ( $file );

        //Подбираем подсветку синтаксиса
        switch ( strtolower ( $pathinfo['extension'] ) )
        {
            case 'xml':
                $parserfiles = '"parsexml.js"';
                $stylesheets = '"/admin/css/CodeMirror/xmlcolors.css"';
                break;

            case 'js':
                $parserfiles = '["tokenizejavascript.js", "parsejavascript.js"]';
                $stylesheets = '"/admin/css/CodeMirror/jscolors.css"';
                break;

            case 'css':
                $parserfiles = '"parsecss.js"';
                $stylesheets = '"/admin/css/CodeMirror/csscolors.css"';
                break;

            case 'htm': case 'html':
                $parserfiles = '["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"]';
                $stylesheets = '["/admin/css/CodeMirror/xmlcolors.css", "/admin/css/CodeMirror/jscolors.css", "/admin/css/CodeMirror/csscolors.css"]';
                break;

            case 'php': case 'tpl':
                $parserfiles = '["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"]';
                $stylesheets = '["/admin/css/CodeMirror/xmlcolors.css", "/admin/css/CodeMirror/jscolors.css", "/admin/css/CodeMirror/csscolors.css", "/admin/css/CodeMirror/phpcolors.css"]';
                break;

            case 'sql':
                $parserfiles = '"parsesql.js"';
                $stylesheets = '"/admin/css/CodeMirror/sqlcolors.css"';
                break;

            default:
                $parserfiles = '"parsedummy.js"';
                $stylesheets = '""';
                break;
        }

        $form = tpl ( 'widgets/file_edit', array (
            'text' => $text,
            'parserfiles' => $parserfiles,
            'stylesheets' => $stylesheets,
            'pathinfo' => $pathinfo,
            'writable' => $writable,
            'form_action' => $form_action,
            'file' => $file
            ) );

        return $form;
    }

    function DataTree ( $table, $options, $fields )
    {

        if ( !isset ( $options['nouns']['top'] ) )
        {
            $this->title = 'Не задано поле иерархии';
            return '<p>Пожалуйста, укажите поле иерахии для таблицы «' . $table . '». Это поле используется для соотношения подразделов к разделам, как правило, называется top, parent и т.д. Задается в секции «nouns» аргументов метода DataTree.</p>';
        }

        //Собираем данные о полях таблицы
        $syscolumns = $this->TableColumns ( $table );
        $deleted_field_exist = false;
        if ( isset ( $options['nouns']['deleted'] ) )
        {
            foreach ( $syscolumns as $i )
            {
                if ( $i['Field'] == $options['nouns']['deleted'] )
                    $deleted_field_exist = true;
            }
        }

        //Добавление
        if ( isset ( $_GET['create'] ) )
        {

            //Фикс для if_empty_make_uri
            $insert_uri_fields = '';
            $insert_uri_values = '';
            foreach ( $fields as $field => $helpers )
            {
                if ( isset ( $helpers['if_empty_make_uri'] ) && $helpers['if_empty_make_uri'] == $options['nouns']['name'] )
                {
                    $insert_uri_fields .= ', `' . $field . '`';
                    $insert_uri_values .= ", '" . SqlTools::escapeString ( makeURI ( $_POST['name'] ) ) . "'";
                }
            }

            //$id = (int)$_POST['id'];
            $name = $_POST['name'];
            $ref_id = ( int ) $_POST['ref_id'];
            switch ( $_POST['type'] )
            {
                case 'before':
                    $tops = SqlTools::selectRows ( "SELECT `" . $options['nouns']['top'] . "` FROM `prefix_$table` WHERE `id` = $ref_id" );
                    $new_top = $tops[0][0];

                    $adds = SqlTools::selectRows ( "
						SELECT
							`" . $options['nouns']['id'] . "`,
							`" . $options['nouns']['order'] . "`
						FROM `prefix_$table`
						WHERE
							`" . $options['nouns']['top'] . "` = $new_top
						ORDER BY `" . $options['nouns']['order'] . "`
					", MYSQL_ASSOC );
                    $branch = array ();
                    foreach ( $adds as $i )
                    {
                        $branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
                    }
                    //Расчет будущего порядка order
                    //$branchItems[0] = $branchItems[$ref_id] - 0.5;

                    asort ( $branchItems );

                    $i = 0;
                    foreach ( $branchItems as $k => $v )
                    {
                        if ( $k == $ref_id )
                        {
                            $lastId = SqlTools::insert ( "
								INSERT INTO `prefix_$table`
									(
										`" . $options['nouns']['name'] . "`,
										`" . $options['nouns']['order'] . "`,
										`" . $options['nouns']['top'] . "`,
										`" . $options['nouns']['created'] . "`
										$insert_uri_fields
									)
								VALUES (
									'" . SqlTools::escapeString ( $name ) . "',
									" . $i++ . ",
									$new_top,
									NOW()
									$insert_uri_values
								)
							" );
                            echo $lastId;
                        }
                        SqlTools::execute ( "
							UPDATE `prefix_$table`
							SET
								`" . $options['nouns']['top'] . "` = $new_top,
								`" . $options['nouns']['order'] . "` = " . $i++ . "
							WHERE `" . $options['nouns']['id'] . "` = $k
						" );
                    }
                    break;

                case 'after':
                    $newTops = SqlTools::selectRows ( "SELECT `" . $options['nouns']['top'] . "` FROM `prefix_$table` WHERE `id` = $ref_id" );
                    $new_top = $newTops[0][0];
                    $adds = SqlTools::selectRows ( "
						SELECT
							`" . $options['nouns']['id'] . "`,
							`" . $options['nouns']['order'] . "`
						FROM `prefix_$table`
						WHERE
							`" . $options['nouns']['top'] . "` = $new_top
						ORDER BY `" . $options['nouns']['order'] . "`
					", MYSQL_ASSOC );
                    $branch = array ();
                    foreach ( $adds as $i )
                    {
                        $branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
                    }
                    //Расчет будущего порядка order
                    asort ( $branchItems );

                    $i = 0;
                    foreach ( $branchItems as $k => $v )
                    {
                        if ( $k == $ref_id )
                        {
                            $lastId = SqlTools::insert ( "
								INSERT INTO `prefix_$table`
									(
										`" . $options['nouns']['name'] . "`,
										`" . $options['nouns']['order'] . "`,
										`" . $options['nouns']['top'] . "`,
										`" . $options['nouns']['created'] . "`
										$insert_uri_fields
									)
								VALUES (
									'" . SqlTools::escapeString ( $name ) . "',
									" . (2 + $i++) . ",
									$new_top,
									NOW()
									$insert_uri_values
								)
							" );
                            echo $lastId;
                        }
                        SqlTools::execute ( "
							UPDATE `prefix_$table`
							SET
								`" . $options['nouns']['top'] . "` = $new_top,
								`" . $options['nouns']['order'] . "` = " . $i++ . "
							WHERE `" . $options['nouns']['id'] . "` = $k
						" );
                    }
                    break;

                case 'inside':
                    $lastId = SqlTools::insert ( "
						INSERT INTO `prefix_$table`
							(
								`" . $options['nouns']['name'] . "`,
								`" . $options['nouns']['order'] . "`,
								`" . $options['nouns']['top'] . "`,
								`" . $options['nouns']['created'] . "`
								$insert_uri_fields
							)
						VALUES (
							'" . SqlTools::escapeString ( $name ) . "',
							0,
							$ref_id,
							NOW()
							$insert_uri_values
						)
					" );
                    echo $lastId;
                    break;
            }
            exit ();
        }

        //Удаление
        if ( isset ( $_GET['delete'] ) )
        {
            $id = ( int ) $_POST['id'];

            //Собираем внутренние топики для удаления
            $recDelTopics = function($topic_id, $options, $table, $recDelTopics)
            {
                $topics_id = array ();
                $topics = SqlTools::selectRows ( "SELECT `{$options['nouns']['id']}` FROM `prefix_$table` WHERE `{$options['nouns']['top']}` = $topic_id", MYSQL_ASSOC );
                foreach ( $topics as $i )
                {
                    $topics_id[] = $i[$options['nouns']['id']];
                    $topics_id = array_merge ( $topics_id, $recDelTopics ( $i[$options['nouns']['id']], $options, $table, $recDelTopics ) );
                }
                return $topics_id;
            };
            $toDelTopics = $recDelTopics ( $id, $options, $table, $recDelTopics );

            //Добавляем указанный верхний к удалению
            $toDelTopics[] = $id;

            //Удаление топиков
            if ( $deleted_field_exist )
            {
                SqlTools::execute ( "UPDATE `prefix_$table` SET `{$options['nouns']['deleted']}` = 'Y' WHERE `{$options['nouns']['id']}` IN (" . implode ( ',', $toDelTopics ) . ")" );
            }
            else
            {
                SqlTools::execute ( "DELETE FROM `prefix_$table` WHERE `{$options['nouns']['id']}` IN (" . implode ( ',', $toDelTopics ) . ")" );
            }

            //Удаление записей подопечной таблицы
            if ( isset ( $options['inner']['table'] ) )
            {
                if ( isset ( $options['inner']['deleted'] ) )
                {
                    SqlTools::execute ( "UPDATE `prefix_{$options['inner']['table']}` SET `{$options['inner']['deleted']}` = 'Y' WHERE "
                        . " `{$options['inner']['top_key']}` IN (" . implode ( ',', $toDelTopics ) . ")" );
                }
                else
                {
                    SqlTools::execute ( "DELETE FROM `prefix_{$options['inner']['table']}` "
                        . " WHERE `{$options['inner']['top_key']}` IN (" . implode ( ',', $toDelTopics ) . ")" );
                }
            }

            exit ();
        }

        //Переименование
        if ( isset ( $_GET['rename'] ) )
        {

            //Фикс для if_empty_make_uri
            $update_uri_fields = '';
            foreach ( $fields as $field => $helpers )
            {
                if ( isset ( $helpers['if_empty_make_uri'] ) && $helpers['if_empty_make_uri'] == $options['nouns']['name'] )
                {
                    $update_uri_fields .= ", `$field` = '" . makeURI ( $_POST['name'] ) . "'";
                }
            }

            $id = ( int ) $_POST['id'];
            $name = $_POST['name'];
            SqlTools::execute ( "
				UPDATE `prefix_$table`
				SET
					`" . $options['nouns']['name'] . "` = '" . SqlTools::escapeString ( $name ) . "'
					$update_uri_fields
				WHERE `" . $options['nouns']['id'] . "` = $id
			" );
            exit ();
        }

        //Смена сортировки или перемещение
        if ( isset ( $_GET['move'] ) )
        {
            $id = ( int ) $_POST['id'];
            $ref_id = ( int ) $_POST['ref_id'];
            switch ( $_POST['type'] )
            {
                case 'before':
                    $newTops = SqlTools::selectRows ( "SELECT `" . $options['nouns']['top'] . "` FROM `prefix_$table` WHERE `id` = $ref_id" );
                    $new_top = $newTops[0][0];
                    $adds = SqlTools::selectRows ( "
						SELECT
							`" . $options['nouns']['id'] . "`,
							`" . $options['nouns']['order'] . "`
						FROM `prefix_$table`
						WHERE
							`" . $options['nouns']['top'] . "` = $new_top
						ORDER BY `" . $options['nouns']['order'] . "`
					", MYSQL_ASSOC );
                    $branch = array ();
                    foreach ( $adds as $i )
                    {
                        $branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
                    }
                    //Вставка итема, который мы переносим (-0.5 как бы намекает что перед нужным итемом)
                    $branchItems[$id] = $branchItems[$ref_id] - 0.5;

                    asort ( $branchItems );

                    $i = 0;
                    foreach ( $branchItems as $k => $v )
                    {
                        SqlTools::execute ( "
							UPDATE `prefix_$table`
							SET
								`" . $options['nouns']['top'] . "` = $new_top,
								`" . $options['nouns']['order'] . "` = " . $i++ . "
							WHERE `" . $options['nouns']['id'] . "` = $k
						" );
                    }
                    break;

                case 'after':
                    $newTops = SqlTools::selectRows ( "SELECT `" . $options['nouns']['top'] . "` FROM `prefix_$table` WHERE `id` = $ref_id" );
                    $new_top = $newTops[0][0];
                    $adds = SqlTools::selectRows ( "
						SELECT
							`" . $options['nouns']['id'] . "`,
							`" . $options['nouns']['order'] . "`
						FROM `prefix_$table`
						WHERE
							`" . $options['nouns']['top'] . "` = $new_top
						ORDER BY `" . $options['nouns']['order'] . "`
					", MYSQL_ASSOC );
                    $branch = array ();
                    foreach ( $adds as $i )
                    {
                        $branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
                    }
                    //Вставка итема, который мы переносим (+0.5 как бы намекает что после нужного итема)
                    $branchItems[$id] = $branchItems[$ref_id] + 0.5;

                    asort ( $branchItems );

                    $i = 0;
                    foreach ( $branchItems as $k => $v )
                    {
                        SqlTools::execute ( "
							UPDATE `prefix_$table`
							SET
								`" . $options['nouns']['top'] . "` = $new_top,
								`" . $options['nouns']['order'] . "` = " . $i++ . "
							WHERE `" . $options['nouns']['id'] . "` = $k
						" );
                    }
                    break;

                case 'inside':
                    SqlTools::execute ( "
						UPDATE `prefix_$table`
						SET
							`" . $options['nouns']['top'] . "` = $ref_id,
							`" . $options['nouns']['order'] . "` = 0
						WHERE `" . $options['nouns']['id'] . "` = $id
					" );
                    break;
            }
            exit ();
        }

        //JSON для дерева
        if ( isset ( $_GET['json'] ) )
        {

            //Строим дерево
            if ( isset ( $options['nouns']['order'] ) )
            {
                $order = $options['nouns']['order'];
            }
            else
            {
                $order = $options['nouns']['id'];
            }
            $where = '';
            if ( $deleted_field_exist )
            {
                $where .= " AND `{$options['nouns']['deleted']}`='N'";
            }
            $trees = SqlTools::selectRows ( "SELECT * FROM `prefix_$table` WHERE 1 $where ORDER BY `$order`", MYSQL_ASSOC );
            $branches = array ();
            foreach ( $trees as $i )
            {
                $branches[$i[$options['nouns']['top']]][$i[$options['nouns']['id']]] = $i;
                //$tree_list[$i[$options['nouns']['id']]] = $i;
            }

            //Количества подопечной таблицы
            if ( isset ( $options['inner']['table'] ) && isset ( $options['inner']['top_key'] ) )
            {
                if ( isset ( $options['inner']['deleted'] ) )
                {
                    $inner_where = "AND `" . $options['inner']['deleted'] . "` = 'N'";
                }
                else
                {
                    $inner_where = '';
                }
                $tables = SqlTools::selectRows ( "
					SELECT
						`" . $options['inner']['top_key'] . "`,
						COUNT(*) AS `counts`
					FROM `prefix_" . $options['inner']['table'] . "`
					WHERE 1 $inner_where
					GROUP BY `" . $options['inner']['top_key'] . "`", MYSQL_ASSOC );
                $innerList = array ();
                foreach ( $tables as $i )
                {
                    $innerList[$i[$options['inner']['top_key']]] = $i['counts'];
                }
            }

            function treeBuild ( $branches, $options, $branch_id = 0, $innerList = array () )
            {
                $branch = array ();
                foreach ( $branches[$branch_id] as $b )
                {
                    //Количества подопечной таблицы
                    if ( isset ( $options['inner']['table'] ) && isset ( $options['inner']['top_key'] ) )
                    {
                        if ( isset ( $innerList[$b[$options['nouns']['id']]] ) && $innerList[$b[$options['nouns']['id']]] > 0 )
                        {
                            $add_count = ' (' . $innerList[$b[$options['nouns']['id']]] . ')';
                        }
                        else
                        {
                            $add_count = '';
                        }
                    }
                    else
                    {
                        $add_count = '';
                    }
                    //Если запись — узел (ветка)
                    if ( isset ( $branches[$b[$options['nouns']['id']]] ) )
                    {
                        $branch[] = array (
                            'data' => array (
                                'title' => $b[$options['nouns']['name']] . $add_count,
                                'attributes' => array
                                    (
                                    'id' => 't' . $b[$options['nouns']['id']],
                                    "data-leaf" => $b[$options['nouns']['leaf']],
                                    "data-last" => "N"
                                )
                            ),
                            'children' => treeBuild ( $branches, $options, $b[$options['nouns']['id']], $innerList )
                        );
                    }
                    //Если запись — лист
                    else
                    {
                        $branch[] = array (
                            'data' => array (
                                'title' => $b[$options['nouns']['name']] . $add_count,
                                //'icon'			=> '/admin/images/icons/document-text-image.png',
                                'attributes' => array
                                    (
                                    'id' => 't' . $b[$options['nouns']['id']],
                                    "data-leaf" => $b[$options['nouns']['leaf']],
                                    "data-last" => "Y"
                                )
                            )
                        );
                    }
                }
                return $branch;
            }
            $tree = treeBuild ( $branches, $options, 0, $innerList );

            $json_tree = json_encode ( $tree );

            echo $json_tree;

            exit ();
        }

        if ( isset ( $options['controls'] ) )
        {
            $listLink = $options['controls']['list'];
        }
        else
        {
            $listLink = '';
        }

        $tree_html = tpl ( 'widgets/tree', array (
            'table' => $table,
            'link' => $this->GetLink (),
            'listLink' => $listLink,
            'moduleName' => $this->called_class
            ) );

        //Форма добавления/редактирования jQueryUI
        $edit = $this->DataTable_AddEdit ( $_SERVER['REQUEST_URI'], $table, $options, NULL, $fields );

        return $tree_html . $edit;
    }

    function Hint ()
    {
        if ( isset ( $this->hint ) && is_array ( $this->hint ) && isset ( $this->hint['text'] ) )
        {
            if ( !isset ( $this->hint['title'] ) )
            {
                $this->hint['title'] = $this->title;
            }
            $ret = tpl ( 'widgets/hint', $this->hint );
            return $ret;
        }
        else
        {
            return '';
        }
    }

    function SubMenu ()
    {
        if ( isset ( $this->submenu ) && is_array ( $this->submenu ) && !empty ( $this->submenu ) && !isset ( $_GET['edit_text'] ) )
        {
            foreach ( $this->submenu as $k => $v )
            {
                $this->submenu[$k] = array ();
                $this->submenu[$k]['act'] = false;
                if ( isset ( $_GET['method'] ) )
                {
                    if ( $_GET['method'] == $k )
                    {
                        $this->submenu[$k]['act'] = true;
                    }
                }
                elseif ( $k == 'Info' )
                {
                    $this->submenu[$k]['act'] = true;
                }
                $this->submenu[$k]['name'] = $v;
                $this->submenu[$k]['link'] = $this->GetLink ( $k, array (), $this->called_class );
            }
            return tpl ( 'widgets/submenu', array ( 'data' => $this->submenu ) );
        }
        else
        {
            return '';
        }
    }

    /** Возвращает для autocomplete массив из:
     * - строки значений, через запятую,  из таблицы autocomplete,
     * - и текущего значения поля $field.
     * Строка значений - для записи $id поля $field таблицы $table (если пусто в поле whereFields),
     * кроме $id, для идентификации запрашивающей записи можно использовать
     * соответствие полей-значений из колонок whereFields и whereValues в таблице autocomplete
     *
     * @param type $table
     * @param type $field
     * @param type $id
     * @return type
     */
    function getAutocompleteValuesList ( $table, $field, $id )
    {
        $query = "SELECT `whereFields` FROM `prefix_autocomplete` WHERE `tableName`='$table' AND `fieldName`='$field'";
        $whereFields = trim ( SqlTools::selectValue ( $query ) );

        if ( !empty ( $whereFields ) )
        {
            $values = SqlTools::selectRow ( "SELECT $whereFields FROM `prefix_$table` WHERE `id`=$id", MYSQLI_ASSOC );
            $whereValues = implode ( ",", array_values ( $values ) );

            $query = " WHERE `whereFields`='$whereFields' AND `whereValues`='$whereValues'";
        }
        else
        {
            $query = " WHERE `tableName`='$table' AND `fieldName`='$field'";
        }

        $query = "SELECT `autocompleteValues` FROM `prefix_autocomplete` " . $query;
        $autocompleteValues = SqlTools::selectValue ( $query );

        return $autocompleteValues;
    }
}
