<?php

class ComponentFactory
{
    private static $instance;

    /**
     * @var type Module пул компонентов
     */
    private static $components;

    /**
     * Отдает объект из пула если он уже создан, иначе создает, помещает в пул и отдает.
     * @param type $componentName Имя компонента
     * @return Component Возвращаемый объект
     */
    public static function getComponent ( $componentName )
    {
        if ( !isset ( self::$components ) )
        {
            self::$components = array ();
        }



        if ( !array_key_exists ( $componentName, self::$components ) )
        {
            $rc = new ReflectionClass ( $componentName );
            if ( $rc->hasMethod ( "getInstance" ) )
            {
                $rm = new ReflectionMethod ( $componentName, "getInstance" );
                $component = $rm->invoke ( null );
            }
            else
            {
                $component = $rc->newInstance ();
            }

            self::$components[$componentName] = $component;
        }

        return self::$components[$componentName];
    }
}
