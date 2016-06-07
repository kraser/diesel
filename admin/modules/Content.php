<?php

class Content extends AdminModule
{
    const name = 'Страницы и содержание';
    const order = 1;
    const icon = 'file-text-o';
    public $submenu = array
    (
        'Info' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Страницы и содержание',
        'siteMap' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Карта сайта'
    );

    public function siteMap ()
    {
        $siteMap = Starter::app ()->content->getSiteMap ();Tools::dump($siteMap);
        $domain = "http://" . $_SERVER['HTTP_HOST'];
        $siteMapXml = "/sitemap.xml";
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
    <url>";
        $xml .= $this->parseSiteMap ( $siteMap, $domain );
        $xml .= "
    </url>
</urlset>
";
        file_put_contents ( DOCROOT . $siteMapXml, $xml );
        if ( is_file ( DOCROOT . $siteMapXml ) )
        {
            if ( is_writable ( DOCROOT . $siteMapXml ) )
            {
                if ( file_put_contents ( DOCROOT . $siteMapXml, $xml ) )
                    $this->content = 'Файл ' . $siteMapXml . ' успешно обновлен.';
                else
                    $this->content = 'Неизвестная ошибка записи';
            }
            else
                $this->content = 'Невозможно записать файл ' . $siteMapXml . ', нет прав! Попробуйте создать его самостоятельно и назначьте права 0777.';
        }

    }

    public function parseSiteMap ( $siteMap, $domain )
    {
        $xml = "";
        foreach ( $siteMap as $doc )
        {
            $url = $domain . $doc->link;
            $xml .= "
        <loc>$url</loc>";
            if ( $doc->docs && count ( $doc->docs ) )
                $xml .= $this->parseSiteMap ( $doc->docs, $domain );

            if ( $doc->children && count ( $doc->children ) )
                $xml .= $this->parseSiteMap ( $doc->children, $domain );
        }

        return $xml;
    }

    function Info ()
    {
        //Модули сайта
        $siteModules = scandir ( DOCROOT . '/system/modules' );
        $notShowingModules = array ( 'Content', 'MainPage' );
        foreach ( $siteModules as $m )
        {
            if ( $m == '.' || $m == '..' )
            {
                continue;
            }

            $m = strstr ( $m, '.', true );
            if ( in_array ( $m, $notShowingModules ) )
            {
                continue;
            }
            $modulesForSelect[] = $m;
        }

        //Топ
        if ( !isset ( $_GET['top'] ) )
        {
            $this->title = 'Страницы и содержание';

            $this->content = $this->DataTable ( 'content', array (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'person' => 'person', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text' // TEXT
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'del'
                ),
                'tabs' => array
                (
                    'Images' => 'Изображения',
                    '_Seo' => 'SEO'/*,
                    '_Regions' => 'Регионы',*/
                )
                ),
                array
                (
                    'id' => array ( 'name' => '№', 'class' => 'min' ),
                    'name' => array ( 'name' => 'Название и подразделы', 'length' => '1-100', 'class' => 'max', 'link' => $this->GetLink () . '&top={id}' ),
                    'person' => array
                    (
                        'name' => 'Автор',
                        'select' => array
                        (
                            //Обязательные
                            'table' => 'users',
                            'name' => array
                            (
                                'fields' => array ( "firstName", "lastName" ),
                                'delim' => " "
                            ),
                            //Необязательные
                            'allow_null' => true,
                            'id' => 'id',
                            'order' => 'id',
                        )
                    ),
                    'text' => array ( 'name' => 'HTML текст страницы', 'hide_from_table' => true ),
                    'anons' => array ( 'name' => 'Описание страницы', 'hide_from_table' => true ),
                    'nav' => array
                    (
                        'name' => 'URI ссылка',
                        'length' => '0-32',
                        'regex' => '/^([a-z0-9-_\\\/]+)?$/i',
                        'regex_error' => 'URI ссылка может быть только из цифр, латинских букв, / и дефиса',
                        'if_empty_make_uri' => 'name'
                    ),
                    'template' => array
                    (
                        'name' => 'Шаблон',
                        'length' => '0-32',
                        'regex' => '/^([a-z0-9-_]*)?$/i',
                        'regex_error' => 'Имя шаблона может состоять только из цифр, латинских букв и дефиса'
                    ),
                    'module' => array
                    (
                        'name' => 'Модуль',
                        'length' => '0-32',
                        'autocomplete' => $modulesForSelect
                    ),
                    'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
                    'showmenu' => array ( 'name' => 'Показывать в меню', 'shortLabel' => 'Меню', 'class' => 'min', 'transform' => 'YesNo' ),
                    'order' => array ( 'name' => 'Порядок', 'class' => 'min' )
                ), '`top` = 0' );
        }
        //Содержание
        else
        {
            $i = SqlTools::selectRows ( "SELECT * FROM `prefix_content` WHERE `id`=" . ( int ) $_GET['top'] );

            $this->title = '<a href="' . $this->GetLink () . '">Разделы</a> → ' . $i[0]['name'];

            $this->content = $this->DataTable ( 'content', array
            (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text' // TEXT
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'del'
                ),
                'tabs' => array
                (
                    'Images' => 'Изображения',
                    '_Seo' => 'SEO'
                )
                ),
                array
                (
                    'id' => array ( 'name' => '№', 'class' => 'min' ),
                    'name' => array ( 'name' => 'Название раздела', 'length' => '1-100', 'link' => $this->GetLink () . '&top={id}' ),
                    'text' => array ( 'name' => 'HTML текст страницы', 'hide_from_table' => true ),
                    'nav' => array
                    (
                        'name' => 'URI ссылка',
                        'length' => '0-32',
                        'regex' => '/^([a-z0-9-_]+)?$/i',
                        'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                        'if_empty_make_uri' => 'name'
                    ),
                    'template' => array
                    (
                        'name' => 'Шаблон',
                        'length' => '0-32',
                        'regex' => '/^([a-z0-9-_]*)?$/i',
                        'regex_error' => 'Имя шаблона может состоять только из цифр, латинских букв и дефиса'
                    ),
                    'module' => array
                    (
                        'name' => 'Модуль',
                        'length' => '0-32',
                        'autocomplete' => $modulesForSelect
                    ),
                    'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
                    'showmenu' => array ( 'name' => 'Показывать в меню', 'class' => 'min', 'transform' => 'YesNo' ),
                    'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                    'top' => array
                    (
                        'name' => 'Раздел',
                        // 'class'     => 'min',
                        'default' => $_GET['top'],
                        'select' => array
                        (
                            //Обязательные
                            'table' => 'content',
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            'order' => 'order',
                            'allow_null' => true,
                            'top' => 'top'
                        )
                    ),
                    'person' => array
                    (
                        'name' => 'Автор',
                        'select' => array
                        (
                            //Обязательные
                            'table' => 'users',
                            'name' => array
                            (
                                'fields' => array ( "firstName", "lastName" ),
                                'delim' => " ",
                            ),
                            'name' => 'firstName',
                            //Необязательные
                            'allow_null' => true,
                            'id' => 'id',
                            'order' => 'id',
                        )
                    )
                ), '`top` = ' . ( int ) $_GET['top'] );
        }
    }

    public function ajaxImages ()
    {
        $result = "";
        if ( isset ( $_GET['files'] ) && empty ( $_FILES ) )
        {
            $files = $_GET['files'];
            foreach ( $files as $file )
            {
                $_FILES['image'] = array ( "tmp_name" => DOCROOT . DS . DATA . DS . $file["path"], "name" => $file["name"] );
                $result = $this->Images ( true );
            }
        }
        htmlHeader ();
        echo $result;
        exit ();
    }

    function Images ( $isNoEcho = false )
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);

        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $info = null;
        $imager = Starter::app ()->imager;

        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['image']['error'] == 1 )
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );

            $imager->addImage ( $_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
            $imager->starImage ( $_GET['star'] );

        //Удаление
        if ( isset ( $_GET['del'] ) )
            $imager->delImage ( $_GET['del'] );

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $imager->getImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => $info,
            ) );

        if ( $isNoEcho )
            return $result;
        else
        {
            echo $result;
            exit ();
        }
    }
}
