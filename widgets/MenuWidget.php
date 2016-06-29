<?php
/**
 * Description of MenuWidget
 *
 * @author kraser
 */
class MenuWidget extends CmsWidget
{
    public $categories;
    public $nn;
    
    public function __construct ( $parent )
    {
        parent::__construct ( "Menu", $parent );
    }

    public function run ()
    {
        $menu = $this->createMenu ();
        return TemplateEngine::view ( "widgets/menu", [ 'menu' => $menu ], null, true );
    }

    private function createMenu ()
    {
        $items =  SqlTools::selectObjects("SELECT id, top, nav, name FROM `prefix_content` WHERE `showmenu`='Y' ORDER BY `order`");
        
        foreach ($items as $key=>$item)
                $menu[$item->top][$item->id] = $item;
        
        $this->categories = $menu;
        
        $menu = $this->buildTree();
        
        return  $menu ;
    }
    
    public function buildTree($parent_id = 0, $only_parent = false)
    {
        $this->categories; 
        echo $parent_nav;
        $uri = $_SERVER['REQUEST_URI'];
        
        if ($parent_id > 0) 
            foreach($this->categories[$parent_id] as $cat)
                $parent_nav = SqlTools::selectValue("SELECT `nav` FROM `prefix_content` WHERE id={$cat->top}").'/';
        else $parent_nav = '';
        
        if(is_array($this->categories) and isset($this->categories[$parent_id])){            
            if ($parent_id == 0) $tree = '<ul class="sf-menu" data-type="navbar">';
                else $tree = '<ul>';
                            
            if($only_parent==false){
                foreach($this->categories[$parent_id] as $cat)
                {                        
                    $class = '/'.$cat->nav == $uri ? 'class="active"' : '';
                    
                    $tree .= '<li '.$class.'><a href="/'.$parent_nav.$cat->nav.'">'.$cat->name;
                    $tree .=  $this->buildTree($cat->id);
                    $tree .= '</a></li>';
                }
            }
            $tree .= '</ul>';
        }
        else return null;
        
        return $tree;   
        
    }
}