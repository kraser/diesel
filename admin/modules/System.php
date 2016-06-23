<?php

class System extends AdminModule
{
    const name = 'Системные функции';
    const order = 100;
    const hide = true;

    function Info ()
    {

        $this->title = 'Системные функции';
        $this->content = tpl ( 'modules/' . __CLASS__ . '/main' );
    }

    function Settings ()
    {
        global $modules;
        $modulesForSelect = array ();
        foreach ( $modules as $m )
        {
            if ( !$m['hide'] )
                $modulesForSelect[] = $m['module'];
        }

        $this->title = 'Настройки модулей';
        $this->hint['text'] = 'Для вставки блока в в любой текст — <code>{block:0}</code><br /><br /> Где 0 — Id или callname нужного блока';
        $this->content = $this->DataTable ( 'settings', array (
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
                'autocomplete' => $modulesForSelect
            ),
            'name' => array ( 'name' => 'Название настройки', 'length' => '1-128' ),
            'callname' => array ( 'name' => 'Имя для вызова', 'length' => '0-128' ),
            'value' => array ( 'name' => 'Значение' )
        ) );
    }

    function CheckFiles ()
    {
        $this->title = 'Состояние файлов';

        $GLOBALS['list_checkfiles'] = array ();

        function ScanDir_CheckFiles ( $dir )
        {

            $allowed_types = array ( 'php', 'js', 'css', 'html', 'htm' );

            $files = scandir ( $dir );
            $files_info = array ();
            foreach ( $files as $file )
            {
                if ( $file == '.' || $file == '..' )
                    continue;

                if ( is_file ( $dir . '/' . $file ) )
                {
                    $ext = explode ( '.', $file );
                    $ext = end ( $ext );
                    if ( !in_array ( $ext, $allowed_types ) )
                        continue;
                    $GLOBALS['list_checkfiles'][$dir . '/' . $file] = array (
                        'file' => $dir . '/' . $file,
                        'name' => $file,
                        'size' => filesize ( $dir . '/' . $file ),
                        'md5' => md5_file ( $dir . '/' . $file ),
                        'date' => filemtime ( $dir . '/' . $file )
                    );
                }
                elseif ( is_dir ( $dir . '/' . $file ) )
                    ScanDir_CheckFiles ( $dir . '/' . $file );
            }
        }
        ScanDir_CheckFiles ( DOCROOT );
        $list = $GLOBALS['list_checkfiles'];
        unset ( $GLOBALS['list_checkfiles'] );

        if ( isset ( $_GET['save'] ) )
            setVar ( 'CheckFiles', serialize ( $list ) );

        $saved = unserialize ( getVar ( 'CheckFiles' ) );

        if ( !$saved )
            setVar ( 'CheckFiles', serialize ( $list ) );

        /*
          $saved — Сохраненное состояние
          $list — Текущее состояние
         */

        $allfiles = array_merge ( $saved, $list );

        foreach ( $allfiles as $file => $info )
        {
            if ( !isset ( $list[$file] ) )
                $status = 'deleted';
            elseif ( !isset ( $saved[$file] ) )
                $status = 'new';
            elseif ( $list[$file]['md5'] != $saved[$file]['md5'] )
                $status = 'modified';
            else
                $status = 'actual';

            $result[] = array_merge ( $info, array ( 'status' => $status ) );
        }

        $this->content = tpl ( 'modules/' . __CLASS__ . '/check_files', array (
            'files' => $result,
            'modulelink' => $this->GetLink ( 'Info' ),
            'savelink' => $this->GetLink () . '&save'
        ) );
    }

    function GenRobotsTxt ()
    {
        $this->content = tpl ( 'modules/' . __CLASS__ . '/robots_txt', array (
            'modulelink' => $this->GetLink ( 'Info' ),
            'savelink' => $this->GetLink () . '&save'
        ) );
    }

    function GenSitemap ()
    {
        $nullPriorityModules = array ( 'Basket' );

        $smi = unserialize ( getVar ( 'SiteMapInfo' ) );

        //Сохранение
        if ( isset ( $_GET['save'] ) )
        {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>http://' . $_SERVER['SERVER_NAME'] . '/</loc>
      <lastmod>' . date ( 'c' ) . '</lastmod>
      <changefreq>' . $smi['main']['freq'] . '</changefreq>
      <priority>' . ($smi['main']['priority'] / 100) . '</priority>
   </url>
	';

            $siteDocuments = SqlTools::selectRows ( "SELECT * FROM `prefix_content` WHERE `deleted`='N' AND `show`='Y'", MYSQL_ASSOC );
            foreach ( $siteDocuments as $p )
                $siteDocuments[$p['id']] = $p;
            foreach ( $siteDocuments as $i )
            {
                $siteDocumentsByParent[$i['top']][$i['id']] = $i;
            }

            function linkById ( $id, $siteDocuments, $siteDocumentsByParent )
            {
                if ( $id == 0 )
                    return;

                $link = linkById ( $siteDocuments[$id]['top'], $siteDocuments, $siteDocumentsByParent ) . '/' . $siteDocuments[$id]['nav'];
                return $link;
            }
            foreach ( $siteDocuments as $page )
            {
                if ( $page['top'] == 0 )
                {
                    $priority = $smi['content2']['priority'];
                    $freq = $smi['content2']['freq'];
                }
                elseif ( $page['top'] == 0 )
                {
                    $priority = $smi['content3']['priority'];
                    $freq = $smi['content3']['freq'];
                }

                if ( in_array ( $page['module'], $nullPriorityModules ) )
                {
                    $priority = 0;
                    $freq = 'never';
                }

                if ( substr ( $page['modified'], 0, 1 ) == 0 )
                    $lastmod = date ( 'c', strtotime ( $page['created'] ) );
                else
                    $lastmod = date ( 'c', strtotime ( $page['modified'] ) );

                $xml .= '
   <url>
      <loc>http://' . $_SERVER['SERVER_NAME'] . getLinkById ( $page['id'], $siteDocuments, Starter::app ()->content->docsByParent ) . '</loc>
      <lastmod>' . $lastmod . '</lastmod>
      <changefreq>' . $freq . '</changefreq>
      <priority>' . number_format ( $priority / 100, 1, '.', '' ) . '</priority>
   </url>';

                //Модули
                if ( !empty ( $page['module'] ) )
                {
                    if ( class_exists ( $page['module'] ) )
                    {
                        $module = new $page['module']();
                        //$module->SiteMapConfig();
                        if ( method_exists ( $module, 'SiteMap' ) )
                        {
                            // array(loc, lastmod, changefreq, priority)
                            $moduleSiteMapArray = $module->SiteMap ();
                            if ( !is_array ( $moduleSiteMapArray ) )
                                echo ('Ошибочка, пожалуйста, отдавайте массив из метода SiteMap модуля ' . $page['module']);
                            foreach ( $moduleSiteMapArray as $module_page )
                            {
                                $xml .= '
   <url>
      <loc>http://' . $_SERVER['SERVER_NAME'] . getLinkById ( $page['id'], $siteDocuments, Starter::app ()->content->docsByParent ) . $module_page['loc'] . '</loc>
      <lastmod>' . date ( 'c', strtotime ( $module_page['lastmod'] ) ) . '</lastmod>
      <changefreq>' . $module_page['changefreq'] . '</changefreq>
      <priority>' . number_format ( $module_page['priority'] / 100, 1, '.', '' ) . '</priority>
   </url>';
                            }
                            unset ( $moduleSiteMapArray );
                        }
                        unset ( $module );
                    }
                }
            }


            $xml .= '
	</urlset>';

            echo '<pre>' . htmlspecialchars ( $xml ) . '</pre>';
        }

        $this->content = tpl ( 'modules/' . __CLASS__ . '/site_map', array (
            'modulelink' => $this->GetLink ( 'Info' ),
            'savelink' => $this->GetLink () . '&save'
        ) );
    }

    function CodeEditor ()
    {
        $this->title = 'Редактирование файлов';
        $this->content = tpl ( 'widgets/code_editor', array ( 'modulelink' => $this->GetLink ( 'Info' ) ) );

        function scan_tree ( $path )
        {
            $h = opendir ( $path );
            $cont = '';
            while ( $f = readdir ( $h ) )
            {
                if ( $f == '.' || $f == '..' )
                    continue;

                if ( !is_dir ( $path . '/' . $f ) && !preg_match ( '/\.(js|css|php)$/', $f ) )
                    continue;

                $dircont = '';
                if ( is_dir ( $path . '/' . $f ) )
                    $dircont = scan_tree ( $path . '/' . $f );
                else
                    $cont .= '<li rel="' . $path . '/' . $f . '"><a href="#">' . $f . '</a></li>';

                if ( $dircont )
                    $cont .= '<li><a href="#">' . $f . '</a>' . $dircont . '</li>';
            }
            closedir ( $h );
            $res = '';
            if ( $cont )
            {
                $res = '<ul>';
                $res .= $cont;
                $res .= '</ul>';
            }
            return $res;
        }
        $tree = array ();
        $tree_str = '<ul>';
        foreach ( array ( 'themes', 'css', 'js' ) as $i )
        {
            if ( !is_dir ( $_SERVER['DOCUMENT_ROOT'] . '/' . $i ) && !is_file ( $_SERVER['DOCUMENT_ROOT'] . '/' . $i ) )
                continue;
            $tree_str .= '<li>';
            $tree_str .= '<a href="#">' . $i . '</a>';
            $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $i;
            $tree_str .= scan_tree ( $path );
            $tree_str .= '</li>';
        }
        $tree_str .= '</ul><div style="clear:both;"></div>';


        $this->hint = array (
            'title' => 'Файлы',
            'text' => '<div id="tpledit_filetree" style="padding-bottom:20px;">' . $tree_str . '</div>'
        );
    }

    function json_editbox ()
    {
        $file = realpath ( $_GET['f'] );

        if ( strpos ( $file, DOCROOT ) !== 0 )
            exit ();

        if ( is_file ( $file ) )
        {
            header ( 'Content-Type: text/plain' );
            echo file_get_contents ( $file );
        }
        exit;
    }

    function json_editbox_options ()
    {
        $data['can_edit'] = is_writable ( $_GET['f'] );
        $pathinfo = pathinfo ( $_GET['f'] ); // dirname basename extension filename
        switch ( strtolower ( $pathinfo['extension'] ) )
        {
            case 'xml':
                $data['parser_files'] = 'parsexml.js';
                $data['style_sheets'] = '/admin/css/CodeMirror/xmlcolors.css';
                break;

            case 'js':
                $data['parser_files'] = array ( 'tokenizejavascript.js', 'parsejavascript.js' );
                $data['style_sheets'] = '/admin/css/CodeMirror/jscolors.css';
                break;

            case 'css':
                $data['parser_files'] = 'parsecss.js';
                $data['style_sheets'] = '/admin/css/CodeMirror/csscolors.css';
                break;

            case 'htm':
            case 'html':
                $data['parser_files'] = array ( 'parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js' );
                $data['style_sheets'] = array ( '/admin/css/CodeMirror/xmlcolors.css', '/admin/css/CodeMirror/jscolors.css', '/admin/css/CodeMirror/csscolors.css' );
                break;

            case 'php': case 'tpl':
                $data['parser_files'] = array ( 'parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'tokenizephp.js', 'parsephp.js', 'parsephphtmlmixed.js' );
                $data['style_sheets'] = array ( '/admin/css/CodeMirror/xmlcolors.css', '/admin/css/CodeMirror/jscolors.css', '/admin/css/CodeMirror/csscolors.css', '/admin/css/CodeMirror/phpcolors.css' );
                break;

            case 'sql':
                $data['parser_files'] = 'parsesql.js';
                $data['style_sheets'] = '/admin/css/CodeMirror/sqlcolors.css';
                break;

            default:
                $parserfiles = '"parsedummy.js"';
                $stylesheets = '""';
                break;
        }
        echo json_encode ( $data );
        exit;
    }

    function file_submit ()
    {
        $file = realpath ( $_REQUEST['file'] );
        if ( strpos ( $file, DOCROOT ) !== 0 )
            exit ();

        if ( $_POST['text'] && $file && is_writable ( $file ) )
        {
            //$f = $_REQUEST['file'];
            $t = $_POST['text'];
            file_put_contents ( $file, $t );
            unset ( $_POST['text'] );
        }
        echo 'ok';
        exit;
    }

    function Users ()
    {
        $this->title = 'Пользователи системы';
        $this->content = $this->DataTable ( 'admin_users', array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'name' => 'login', // VARCHAR
                //'order'		=> 'order',		// INT
                //'deleted'	=> 'deleted',	// ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
            //'text'		=> 'text',		// TEXT
            //'image'		=> true
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit',
                'del'
            ),
            'tabs' => array (
                'userAccess' => 'Доступ пользователя'
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'login' => array ( 'name' => 'Логин (имя пользователя)', 'length' => '1-32', 'regex' => '/^([a-z0-9-_]+)?$/i', 'regex_error' => 'Логин должен состоять только из латинских букв и цифр' ),
            'password' => array ( 'name' => 'Пароль (хэш)', 'hide_from_table' => true, 'beforeInsert' => 'md5', 'beforeUpdate' => 'md5' ),
            'name' => array ( 'name' => 'Полное имя' ),
            'post' => array ( 'name' => 'Должность' ),
            'email' => array ( 'name' => 'Почта' ),
            'lastenter' => array ( 'name' => 'Последний вход' ),
            'type' => array ( 'name' => 'Администратор / менеджер' )
        ) );


        // — Скажи пароль? — Дер пароль!
        if ( isset ( $_POST['id'] ) && $_POST['id'] != 0 && !empty ( $_POST['password'] ) && !preg_match ( '/\A[0-9abcdef]{32}\z/i', $_POST['password'] ) )
        {
            SqlTools::execute ( "UPDATE `prefix_admin_users` SET `password`='" . md5 ( $_POST['password'] ) . "' WHERE `id`=" . ( int ) $_POST['id'] );
        }
    }

    function userAccess ()
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $message = '';

        //Сохранение
        if ( !empty ( $_POST ) )
        {
            $access_save = array ();
            foreach ( $_POST['access'] as $a => $v )
            {
                $access_save[$a] = true;
            }
            $access_save = serialize ( $access_save );
            SqlTools::execute ( "UPDATE `prefix_admin_users` SET `access`='" . SqlTools::escapeString ( $access_save ) . "' WHERE `id`=$id" );
            $message = 'Сохранено';
        }

        $user = SqlTools::selectRow ( "SELECT * FROM `prefix_admin_users` WHERE `id`=$id", MYSQL_ASSOC );
        $access = unserialize ( $user['access'] );

        //Модули
        $modules = array ();
        $mods = scandir ( DOCROOT . '/admin/modules', 1 );
        foreach ( $mods as $file )
        {
            if ( substr ( $file, -3, 3 ) == 'php' )
            {
                $module_name = substr ( $file, 0, -4 );

                if ( isset ( $access[$module_name] ) )
                    $access_module = true;
                else
                    $access_module = false;

                $modules[] = array (
                    'module' => $module_name,
                    'name' => $module_name::name,
                    'hide' => ($module_name::hide) ? ($module_name::hide) : false,
                    'access' => $access_module
                );
            }
        }

        //Если админ
        if ( $user['type'] == 'a' )
            $message = 'Администратор имеет доступ ко всем разделам, эти настройки на него не влияют';

        echo tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'link' => $this->GetLink (),
            'id' => $id,
            'modules' => $modules,
            'message' => $message
        ) );
        exit ();
    }

    // для работы с хандлерами elFinder-------------------------------
    function ajaxAddToImagesStorage ()
    {
        $result = 0;
        if ( isset ( $_GET['files'] ) )
        {
            $files = $_GET['files'];
            foreach ( $files as $hash )
            {
                // путь от каталога DOCROOT
                $filePath = self::decodeHashPath ( $hash );
                $result = self::addImagesStorageRow ( $filePath );
            }
        }
        return $result;
    }

    /** Принимает данные от хандлера переименования файла/каталога
     * в виде массива files[adds[]rems[]]
     * Предполагается переименование одного пункта за раз
     * @return type
     */
    function ajaxDelFromImagesStorage ()
    {
        $result = false;
        if ( isset ( $_GET['files'] ) )
        {
            $files = $_GET['files'];
            foreach ( $files as $hash )
            {
                // путь от каталога DOCROOT
                $filePath = self::decodeHashPath ( $hash );
                $result = self::delImagesStorageRow ( $filePath );
            }
        }
        return $result;
    }

    /** Принимает данные от хандлера перемещения файла/каталога
     * в виде массива files[adds[]rems[]]
     * @return type
     */
    function ajaxMoveInImagesStorage ()
    {
        $result = false;
        if ( !isset ( $_GET['adds'] ) && !isset ( $_GET['rems'] ) )
        {
            return $result;
        }

        if ( !isset ( $_GET['adds'] ) )
        {
            $adds = array ();
        }

        if ( !isset ( $_GET['rems'] ) )
        {
            $rems = array ();
        }

        $adds = $_GET['adds'];
        $rems = $_GET['rems'];
        for ( $i = 0; $i < count ( $adds ); $i++ )
        {
            //?? обработка ошибок
            $pathBef = self::decodeHashPath ( $rems[$i] );
            $pathAft = self::decodeHashPath ( $adds[$i] );
            $result = self::updImagesStorageRow ( $pathBef, $pathAft );
        }
        return $result;
    }

    function ajaxRenameInImagesStorage ()
    {
        $result = false;
        if ( !isset ( $_GET['adds'] ) && !isset ( $_GET['rems'] ) )
        {
            return $result;
        }

        if ( !isset ( $_GET['adds'] ) )
        {
            $adds = array ();
        }

        if ( !isset ( $_GET['rems'] ) )
        {
            $rems = array ();
        }

        $adds = $_GET['adds'];
        $rems = $_GET['rems'];
        //?? обработка ошибок
        // Предполагается переименование одного пункта за раз
        $pathBef = self::decodeHashPath ( $rems[0] );
        $pathAft = self::decodeHashPath ( $adds[0] );
        $result = self::updImagesStorageRow ( $pathBef, $pathAft );
        return $result;
    }

    /**
     * Приводит в соответствие записи в таблице prefix_images_storage
     * с файлами в каталоге
     */
    function ajaxRefreshImagesStorage ()
    {
        $result = false;
        // все записи из базы
        $pathes = array ();
        $query = "SELECT `path` FROM `prefix_images_storage` ORDER BY `path`";
        $rows = SqlTools::selectRows ( $query );
        foreach ( $rows as $key )
        {
            array_push ( $pathes, $key["path"] );
        }
        // все файлы из хранилища, рекурсивно
        $files = scanDirRecurs ( DOCROOT . DS . IMGS );
        // расхождения
        // есть в базе - нет в каталоге
        $thereInDb = array_diff ( $pathes, $files );
        // есть в каталоге - нет в базе
        $thereInDir = array_diff ( $files, $pathes );

        // добавляем записи
        foreach ( $thereInDir as $path )
        {
            $result = self::addImagesStorageRow ( $path );
        }
        // удаляем записи
        foreach ( $thereInDb as $path )
        {
            $result = self::delImagesStorageRow ( $path );
        }
    }

    private static function decodeHashPath ( $hash )
    {
        $str = str_replace ( DOCROOT, "", IMGS . DS . decode64Elf ( $hash ) );
        return $str;
    }

    private static function getIdImagesStorageRow ( $path )
    {
        $sql = "SELECT `id` FROM `prefix_images_storage` WHERE `path` = '{$path}'";
        $id = SqlTools::selectRow ( $sql );
        $id = $id["id"];
        return $id;
    }

    private static function addImagesStorageRow ( $path )
    {
        $id = self::getIdImagesStorageRow ( $path );
        if ( $id )
        { //?? такая запись уже есть в таблице, ?? что делать
            return false;
        }
        $file = basename ( $path );
        $sql = "INSERT INTO `prefix_images_storage` (`name`, `path`, `created`)
                        VALUES ('$file', '$path', NOW())";
        $id = null;
        $id = SqlTools::insert ( $sql );
        if ( !$id )
        {
            //?? ошибка
            return false;
        }
        return $id;
    }

    private static function delImagesStorageRow ( $path )
    {
        $result = false;
        $sql = "DELETE FROM `prefix_images_storage` WHERE `path` = '$path'";
        $result = SqlTools::execute ( $sql );
        return $result;
    }

    private static function updImagesStorageRow ( $pathBef, $pathAft )
    {
        $result = false;
        $id = self::getIdImagesStorageRow ( $pathBef );
        if ( !$id )
        {
            return $result;
        }
        // изменяем запись
        $fileNameAft = basename ( $pathAft );
        $sql = "UPDATE `prefix_images_storage` SET "
            . "`name`='$fileNameAft', `path`='$pathAft', `modified`=NOW() "
            . "WHERE `id`=$id";
        $result = SqlTools::execute ( $sql );

        return $result;
    }
}
