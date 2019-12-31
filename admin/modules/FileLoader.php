<?php

class FileLoader extends AdminModule
{
    const name = 'Загрузка документов';
    const order = 40;
    const icon = 'table';

    private $table = 'files';
    private $filesDir = DOCUMENTS;
    private $allowedExts = [ 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'rtf', 'txt', 'pdf' ];

    function Info ()
    {
        $this->title = 'Документы';
        $_GET['orderd'] = 'DESC';
        $this->content = $this->DataTable ( $this->table,
        [
            //Имена системных полей
            'nouns' =>
            [
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'link' => 'link', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'del'
            ],
            //Табы (методы этого класса)
            'tabs' =>
            [
                'docFile' => 'Файл документа',
                '_Seo' => 'SEO'/*,
                '_Regions' => 'Регионы'*/
            ]
        ],
        [
            'id' => [ 'name' => '№', 'class' => 'min' ],
            'order' => [ 'name' => 'Порядковый номер' ],
            'alias' => [ 'name' => 'Категория' ],
            'name' => array ( 'name' => 'Наименование документа', 'length' => '1-250' ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min', 'default' => 'Y' ),
            'include' => array ( 'name' => 'В общий архив', 'class' => 'min', 'default' => 'Y' ),
            'date' => array ( 'name' => 'Дата создания', 'transform' => function($str)
            {
                return DatetimeTools::inclinedDate ( $str );
            } )
        ], '', 'date' );

        $this->hint['text'] = 'Вы можете добавить файлы прайсов для скачивания или удалить их в свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" />.';
    }

    public function docFile ()
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $info = null;

        //Добавление файла прайса
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['file']['error'] )
                $info = FileTools::getPhpUploadError ( $_FILES['file']['error'] );

            $this->addFile ( $_FILES['file']['tmp_name'], $_FILES['file']['name'], $id, DOCUMENTS );
        }

        //Удаление файла прайса
        if ( isset ( $_GET['del'] ) )
            $this->delFile ( $_GET['del'], $id ); // должны быть ИД записи и (м.б.) ссылка на файл от корня сайта

        $fileLink = $this->getFile ( $id );
        $actLink = $this->GetLink ();
        $mime = pathinfo ( $fileLink['link'] );
        $mimeExt = array_key_exists ( "extension", $mime ) ? $mime['extension'] : "";
        $mimeIcon = Tools::getMimeIconForExt ( $mimeExt );
        echo tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'file' => $fileLink,
            'link' => $actLink,
            'module_id' => $id,
            'mime' => $mimeIcon,
            'info' => $info,
        ) );
        exit ();
    }

    private function getDocFileLink ( $id )
    {
        if ( !$id )
            return false;

        $sql = "SELECT `link` FROM `prefix_$this->table` WHERE `id`=$id";
        $doc = SqlTools::selectValue ( $sql, MYSQLI_ASSOC );

        return $doc;
    }

    private function delDocFileLink ( $id )
    {
        if ( !$id )
            return false;

        $sql = "UPDATE `prefix_$this->table` SET `link`='' WHERE `id`=$id";
        $price = SqlTools::execute ( $sql, MYSQLI_ASSOC );

        return $price;
    }

    private function getFile ( $id )
    {
        $file = [ "link" => "", "found" => false ];

        $doc = $this->getDocFileLink ( $id );
        if ( !$doc )
            return $file;

        $file["link"] = $doc;
        //сверяем с каталогом
        $file["found"] = is_file ( DOCROOT . $doc );

        return $file;
    }

    private function addFile ( $file, $fileName, $id, $dir = "" )
    {
        if ( !$id || $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        if ( is_file ( $file ) )
        {
            $ext = '';
            if ( !empty ( $fileName ) )
            {
                $fileInfo = pathinfo ( $fileName );
                $ext = strtolower ( $fileInfo['extension'] );
            }

            $this->filesDir = $dir != "" ? $dir : $this->filesDir;
            if ( !is_dir ( $this->filesDir ) )
                FileTools::createDir ( $this->filesDir );

            $dest = $this->filesDir . DS . $fileName;
            if ( copy ( $file, $dest ) )
            {
                $fileLink = str_replace ( DOCROOT, "", $dest );
                $sql = "UPDATE `prefix_$this->table` SET `link`='$fileLink' WHERE `id`=$id";
                SqlTools::execute ( $sql );
            }
        }
    }

    /** Удаляет файл прайса
     * @param integer $id - ИД записи прайса в prefix_prices
     * @return boolean
     */
    private function delFile ( $link, $id )
    {
        $doc = $this->getDocFileLink ( $id );
        if ( !$price )
            return false;

        $file = DOCROOT . $doc;
        if ( is_file ( $file ) )
            $result = unlink ( $file );

        $result = $this->delDocFileLink ( $id ) && $result;
        return $result;
    }
}