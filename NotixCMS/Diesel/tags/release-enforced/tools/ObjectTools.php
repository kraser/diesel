<?php
/**
 * <pre>ObjectTools Вспомогательные функции для работы с объектами</pre>
 *
 * @author kraser
 */
class ObjectTools
{
    /**
     * <pre>Объединение свойств объектов в один объект.
     * За основу берётся первый объект (клонируется), и в него добавляются свойства перечисленных следом объектов.
     * Если у объектов есть одноимённые свойства, в результате остаётся свойство последнего объекта.</pre>
     * @param Mixed $firstObject <p>Первый объект</p>
     */
    public static function merge( $firstObject /* , ... */ )
    {
        $result = clone $firstObject;
        for ( $i = 1; $i < func_num_args(); ++$i )
        {
            self::copyFields( $result, func_get_arg( $i ) );
        }
        return $result;
    }

    private static function copyFields( &$x, $y )
    {
        foreach ( get_object_vars( $y ) as $field => $value )
        {
            $x->$field = $value;
        }
    }

}
