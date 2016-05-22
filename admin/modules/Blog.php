<?php

class Blog extends AdminModule
{
    const name = 'Блог';
    const icon = 'comments';
    const order = 41;

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
        $images = new Images();

        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['image']['error'] == 1 )
            {
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );
            }
            $images->AddImage ( $_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
        {
            $images->StarImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_GET['del'] ) )
        {
            $images->DelImage ( $_GET['del'] );
        }

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $images->GetImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => $info,
        ) );

        if ( $isNoEcho )
        {
            return $result;
        }
        else
        {
            echo $result;
            exit ();
        }
    }

    function Info ()
    {
        //Таблица новостей
        if ( isset ( $_GET['top'] ) )
        {
            $i = SqlTools::selectRows ( "SELECT * FROM `prefix_blog_topics` WHERE `id`=" . ( int ) $_GET['top'] );

            $this->title = '<a href="' . $this->GetLink () . '">Блог</a> → ' . $i[0]['name'];

            $this->content = $this->DataTable ( 'blog', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text', // TEXT
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit',
                    'del'
                ),
                //Табы (методы этого класса)
                'tabs' => array (
                    'Images' => 'Изображения',
                    '_Seo' => 'SEO',
                    'Tags' => 'Теги'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Имя новости', 'length' => '1-128' ),
                'anons' => array ( 'name' => 'Анонс новости', 'hide_from_table' => true ),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
                'date' => array ( 'name' => 'Дата публикации', 'transform' => function($str)
                    {
                        return DatetimeTools::inclinedDate ( $str );
                    } ),
                'top' => array (
                    'name' => 'Раздел',
                    'default' => ( int ) $_GET['top'],
                    'hide_from_table' => true,
                    'select' => array (
                        //Обязательные
                        'table' => 'blog_topics',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        //'allow_null'=> true,
                        'top' => 'top',
                        'deleted' => 'deleted'
                    )
                ),
                ), '`top` = ' . ( int ) $_GET['top'], 'date' );
        }
        //Дерево разделов
        else
        {
            $this->title = 'Блог';
            $this->content = $this->DataTree ( 'blog_topics', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text', // TEXT
                    'top' => 'top'
                ),
                //Отображение контролов
                'controls' => array (
                    'add_root',
                    'add_sub',
                    'edit',
                    'list' => $this->GetLink () . '&top={id}',
                    'del'
                ),
                //Зависимая таблица (напрмер товары или новости по рубрикам)
                'inner' => array (
                    'table' => 'blog', //Имя таблицы
                    'top_key' => 'top', //Ключ соответствия категории товарам
                    'deleted' => 'deleted' //Поле «удалено»
                ),
                //Табы (методы этого класса)
                'tabs' => array (
                    'Images' => 'Изображения',
                    '_Seo' => 'SEO'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Наименование', 'length' => '1-128', 'link' => $this->GetLink () . '&top={id}' ),
                'nav' => array ( 'name' => 'URI ссылка', 'length' => '0-32', 'regex' => '/^([a-z0-9-_]+)?$/i', 'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса', 'if_empty_make_uri' => 'name' ),
                'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                'content_top' => array (
                    'name' => 'Относится к разделам (для примера, не рабочее поле)',
                    'multiselect' => array (
                        //Обязательные
                        'table' => 'content',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        'top' => 'top',
                        'deleted' => 'deleted',
                        'size' => 5
                    )
                ),
                'rate' => array ( 'name' => 'Рейтинг (количество просмотров)' )
                )
            );
        }
    }

//	function Info()
//    {
//		$this->title = 'Блог';
//		$_GET['orderd'] = 'DESC';
//		$this->content = $this->DataTable('blog',array(
//			//Имена системных полей
//			'nouns'	=> array(
//				'id'		=> 'id',		// INT
//				'name'		=> 'name',		// VARCHAR
//				//'order'		=> 'order',		// INT
//				'deleted'	=> 'deleted',	// ENUM(Y,N)
//				'created'	=> 'created',	// DATETIME
//				'modified'	=> 'modified',	// DATETIME
//				'text'		=> 'text',		// TEXT
//				//'image'		=> true
//			),
//			//Отображение контролов
//			'controls' => array(
//				'add',
//				'edit',
//				'del'
//			),
//			//Табы (методы этого класса)
//			'tabs'	=> array(
//				'Images'		=> 'Изображения',
//				'_Seo'			=> 'SEO',
//				'Tags'			=> 'Теги'
//			)
//		),
//		array(
//			'id' 		=> array('name' => '№', 'class' => 'min'),
//			'name'		=> array('name' => 'Имя новости', 'length'=>'1-128'),
//			'anons'		=> array('name' => 'Анонс новости', 'hide_from_table'=>true),
//			'show'		=> array('name' => 'Показывать', 'class'=>'min'),
//			'date'		=> array('name' => 'Дата публикации', 'transform'=>function($str){ return DatetimeTools::inclinedDate($str); }),
//			'tags'		=> array('name' => 'Теги', 'hide_from_table' => true),
//            'tags' => array
//            (
//                'name' => 'Теги',
//                'select' => array
//                (
//                    //Обязательные
//                    'table' => 'tags',
//                    'name' => 'tags_name_id',
//                )
//            ),
//			//'order'		=> array('name' => 'Порядок', 'class'=>'min')
//		),'','date');
//
//		$this->hint['text'] = 'Вы можете добавить анонс новости в ее свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" /><br>или изменить саму новость в редактировании содержания <img src="/admin/images/icons/document-text-image.png" style="vertival-align:middle" />';
//	}

    public function Tags ()
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        if ( !empty ( $_POST['tags'] ) )
        {
            SqlTools::execute ( "DELETE FROM `prefix_tags` WHERE `module_name`='" . __CLASS__ . "' AND `element_id`='" . $id . "'" );
            $data = explode ( ',', $_POST['tags'] );
            foreach ( $data as $tag )
            {
                // SqlTools::execute("DELETE FROM `prefix_tags_name` WHERE `name`='" . trim($tag) . "'");
                $tag_id = SqlTools::insert ( "INSERT INTO `prefix_tags_name` (name)
                                            SELECT * FROM (SELECT '" . trim ( $tag ) . "') AS `tmp`
                                            WHERE NOT EXISTS (
                                                SELECT name FROM `prefix_tags_name` WHERE `name` = '" . trim ( $tag ) . "'
                                            ) LIMIT 1" );
                if ( !$tag_id )
                {
                    $tag_id = SqlTools::selectValue ( "SELECT * FROM `prefix_tags_name` WHERE `name`='" . trim ( $tag ) . "' LIMIT 1" );
                }
                SqlTools::insert ( "INSERT INTO `prefix_tags` (`module_name`, `element_id`, `tags_name_id`)
                                  VALUES ('" . __CLASS__ . "', '" . $id . "', '" . $tag_id . "')" );
            }
            $resultMessage = array (
                'success' => 1
            );

            echo json_encode ( $resultMessage );
            exit ( 'Сохранено!' );
        }

        $tags = SqlTools::selectRows ( "SELECT tn.id, tn.name FROM `prefix_tags_name` AS tn
                                      LEFT JOIN `prefix_tags` AS t ON (t.`tags_name_id` = tn.`id`)
                                      WHERE t.`module_name`='" . __CLASS__ . "' AND t.`element_id`='" . $id . "'
                                            AND t.`show`='Y' AND t.`deleted`='N' AND tn.`deleted`='N'", MYSQL_ASSOC );

        $string = '';
        foreach ( $tags as $tag )
        {
            $string .= $tag['name'] . ',';
        }

        $allTags = SqlTools::selectRows ( "SELECT id, name FROM `prefix_tags_name`
                                         WHERE `deleted`='N'", MYSQL_ASSOC );
        ;
        $sampleTags = '';
        foreach ( $allTags as $tag )
        {
            $sampleTags .= '"' . $tag['name'] . '", ';
        }


        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'module' => __CLASS__,
            'module_id' => $id,
            'tags' => $string,
            'allTags' => $sampleTags,
            'link' => $this->GetLink ( 'Tags', array (), __CLASS__ ),
        ) );

        echo $result;
        exit ();
    }
}
