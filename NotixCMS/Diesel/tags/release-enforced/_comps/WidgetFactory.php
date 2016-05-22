<?php

/**
 * Description of WidgetFactory
 *
 * @author kraser
 */
class WidgetFactory extends CmsComponent
{
    private $widgets;
    
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
    }
    
    public function createWidget ( $owner, $className, $properties = [] )
    {
        $className = Starter::import ( $className, true );
		$widget = new $className ( $owner );

		if ( isset ( $this->widgets[$className] ) )
			$properties = $properties === [] ? $this->widgets[$className] : ArrayTools::merge ( $this->widgets[$className], $properties );
//		if($this->enableSkin)
//		{
//			if($this->skinnableWidgets===null || in_array($className,$this->skinnableWidgets))
//			{
//				$skinName=isset($properties['skin']) ? $properties['skin'] : 'default';
//				if($skinName!==false && ($skin=$this->getSkin($className,$skinName))!==array())
//					$properties=$properties===array() ? $skin : CMap::mergeArray($skin,$properties);
//			}
//		}
        
		foreach ( $properties as $name => $value )
        {
			$widget->$name = $value;
        }
		return $widget;
    }
}
