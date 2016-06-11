<?php
/**
 * Description of MenuWidget
 *
 * @author kraser
 */
class MenuWidget extends CmsWidget
{
    public function __construct ( $parent )
    {
        parent::__construct ( "Menu", $parent );
    }

    public function render ()
    {
        return TemplateEngine::view ( "widgets/menu", [], null, true );
    }

    private function createMenu ()
    {
        $docs = Starter::app ()->content->docs;
        $menuByTop = [];
        $menu = [];
        $activeSet = false;
        foreach ( $docs as $dox )
        {
            $i = (array) $dox;
            if ( $doc->showMenu == 'N' )
                continue;
            //Отмечаем текущую страницу
            if ( in_array ( $doc->nav, Starter::app ()->urlManager->linkPath ) )
            {
                $doc->active = true;
                $activeSet = true;
            }
            else
                $doc->active = false;

            $menuByTop[$i['parentId']][] = $i;
        }
        if ( empty ( $menuByTop[0] ) )
            return tpl ( 'parts/mainmenu', array ( 'menu' => array () ) );

        foreach ( $menuByTop[0] as $top => $i )
        {
            $item['root'] = $i;
            $item['sub'] = [];

                //Проверка на подменю из модуля
            if ( $i['module'] !== "Content" )
            {
                $obj = Starter::app ()->getModule ( $i['module'] );
                if ( method_exists ( $obj, 'SubMenu' ) )
                    $item['sub'] = $obj->SubMenu ();
            }
            else
            {
                if ( isset ( $menuByTop[$i['id']] ) )
                {
                    foreach ( $menuByTop[$i['id']] as $id => $j )
                    {
                        $item['sub'][] = $j;
                    }
                }
            }
            $menu[] = $item;
        }
        return tpl ( 'parts/mainmenu', array ( 'menu' => $menu ) );
    }
}