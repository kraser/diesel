<?php

/**
 * <pre>Компонент Header - формирование и вывод тэга head</pre>
 */
class Header extends CmsComponent
{
    private $model;

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $model = new HeaderFields();
        $model->charset = _CHARSET;
        $model->meta = array ();
        $model->metaText = "";
        $model->js = array ();
        $model->css = array ();
        $model->style = array ();
        $model->script = '';
        $model->title = Starter::app ()->title;
        $this->model = $model;
    }

    public function init ()
    {
        $theme = Starter::app ()->getTheme();
        $path = Starter::getAliasPath ( "webroot.site" );
        if ( file_exists ( Starter::getAliasPath ( "webroot.site" ) . DS . $theme . DS . "head.php" ) )
            include Starter::getAliasPath ( "webroot.site" ) . DS . $theme . DS . "head.php";
        else
            include Starter::getAliasPath ( "webroot" ) . DS . $theme . DS . "head.php";
        parent::init();
    }

    /**
     * <pre>Устанавливает favicon для вывода</pre>
     * @param String $favicon <p>pathname к favicon</p>
     */
    public function setFavicon ( $favicon )
    {
        $this->model->favicon = $favicon;
    }

    /**
     * <pre>Устанавливает заголовок страницы</pre>
     * @param type $title <p>Текст заголовка</p>
     */
    public function setTitle ( $title )
    {
        $this->model->title = $title;
    }

    /**
     * <pre>Устанавливает заголовок страницы</pre>
     * @param type $title <p>Текст заголовка</p>
     */
    public function getTitle ()
    {
        return $this->model->title;
    }

    /**
     * <pre>Формирует пользовательские метатеги</pre>
     * @param String $meta <p>Текст метатега</p>
     */
    public function addMetaText ( $meta )
    {
        $this->model->metaText .= $meta . "\r\n";
    }

    /**
     * <pre>Устанавливает ключевые слова</pre>
     * @param String $keyWords <p>Ключевые слова</p>
     */
    public function setKeyWords ( $keyWords )
    {
        $hash = md5 ( $keyWords );
        if ( !array_key_exists ( $hash, $this->model->keywords ) )
            $this->model->keywords[$hash] = $keyWords;
    }

    /**
     * <pre>Формирует список скриптов js для загрузки</pre>
     * @param String $script <p>pathname загружаемого скрипта</p>
     */
    public function addJs ( $script )
    {
        $hash = md5 ( $script );
        if ( !in_array ( $hash, $this->model->js ) )
            $this->model->js[$hash] = $script;
    }

    /**
     * <pre>Формирует список css-файлов для загрузки</pre>
     * @param String $style <p>pathname загружаемого файла стилей</p>
     * @param String $media <p>Определяет устройство вывода, для работы с которым предназначена таблица стилей.</p>
     */
    public function addCss ( $style, $media = "screen" )
    {
        $hash = md5 ( $style );
        if ( !array_key_exists ( $media, $this->model->css ) )
            $this->model->css[$media] = [];
        if ( !array_key_exists ( $hash, $this->model->css[$media] ) )
            $this->model->css[$media][$hash] = $style;
    }

    /**
     * <pre>Формирует текст js скрипта</pre>
     * @param String $script <p>Текст скрипта</p>
     */
    public function addScript ( $script )
    {
        $this->model->script .= $script . "\n";
    }

    /**
     * <pre>Формирует текст css стилей</pre>
     * @param String $style <p>Текст стилей</p>
     */
    public function addStyle ( $style )
    {
        $this->model->style .= $style . "\n";
    }

    public function addFileLinks($fileLink)
    {
        $this->fileLinks .= $fileLink;
    }

    public function getFileLinks()
    {
        return $this->fileLinks;
    }

    public function setTagH1 ( $tag )
    {
        $this->model->tagH1 = $tag;
    }

    public function getTagH1 ()
    {
        return $this->model->tagH1;
    }

    public function seoSettings ( $controller )
    {
        $entity = $controller->currentDocument;
        $modelName = $controller->model;
        if ( $entity && $modelName )
        {
            $query = "SELECT `title`, `keywords`, `description`, `tagH1` FROM `prefix_seo`
                WHERE `module`='$modelName' AND `module_id`=$entity->id";

            $seo = SqlTools::selectRow ( $query, MYSQL_ASSOC );
            if ( !empty ( $seo ) )
            {
                if ( !empty ( $seo['keywords'] ) )
                    $this->setKeyWords ( htmlspecialchars ( $seo['keywords'] ) );

                if ( !empty ( $seo['description'] ) )
                    $this->addMetaText ( htmlspecialchars ( $seo['description'] ) );

                if ( !empty ( $tseo['title'] ) )
                    $this->setTitle ( $seo['title'] );
                else
                    $this->setTitle ( Starter::app ()->title . "  — " . $entity->title );

                if ( !empty ( $tseo['tagH1'] ) )
                    $this->setTagH1 ( $seo['tagH1'] );
                else
                    $this->setTagH1 ( $entity->title );
            }
            else
            {
                $this->setTitle ( Starter::app ()->title . "  — " . $entity->title );
                $this->setTagH1 ( $entity->title );
            }
        }
    }

    public function getHead ()
    {
        return $this->model;
    }
}

/**
 * <pre>Объект модель тега head</pre>
 */
class HeaderFields
{
    public $favicon;
    public $title;
    public $meta;
    public $metaText;
    public $keywords;
    public $charset;
    public $js;
    public $css;
    public $style;
    public $script;
    public $tagH1;

}
