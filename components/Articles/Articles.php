<?php

class Articles extends CmsModule
{
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->defaultController = 'article';
        $this->template = "page";
        Starter::import ( "Articles.controllers.*" );
        Starter::import ( "Articles.models.*" );
        $this->actions =
        [
            'default' =>
            [
                'method' => 'articleList'
            ],
            'articles' =>
            [
                'method' => 'articleList'
            ],
            'article' =>
            [
                'method' => 'articleView'
            ]
        ];
    }

    public function Run ()
    {
        $action = $this->createAction ();
        if ( !$action )
            page404 ();

        $content = $action->run ();
        return $content;
    }

    public function startController ( $method, $params )
    {
        //$method = $this->path['method'];
        return $this->$method ( $params );
    }

    public function beforeRender ()
    {
        if ( parent::beforeRender () )
        {
            $header = Starter::app ()->headManager;

            return true;
        }
        else
            return false;

    }

    private function articleView ( $params )
    {
        $this->template = "page";
        $manager = new ArticleManager ();
        $article = ArrayTools::head ( $manager->find ( $params ) );
        $article->date = DatetimeTools::inclinedDate ( $article->date );
        $this->title = $article->title;
        $this->model = "article";

        return $this->render ( "article", [ 'article' => $article ] );
    }

    private function articleList ( $params )
    {
        $this->template = "page";
        $manager = new ArticleManager ();
        $articles = ArrayTools::head ( $manager->find ( $params ) );
        $this->title = $article->title;
//        $this->model = "article";

        return $this->render ( "article", [ 'article' => $article ] );
    }
}
