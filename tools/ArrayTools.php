<?php

class ArrayTools
{
    /**
     * <pre>Выбирает из массива объекты, у которых значение поля $field совпадает с искомым $value</pre>
     * @param Array $array <p>Массив объектов для поиска</p>
     * @param String $field <p>Имя свойства объекта, по которому идет поиск</p>
     * @param Mixed $value <p>Значение для отбора</p>
     */
    public static function select ( $array, $field, $value )
    {
        $result = array ();
        if ( is_array ( $array ) && $field )
        {
            foreach ( $array as $item )
            {
                if ( $value == $item->$field )
                    $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * <pre>Создание карты из массива объектов</pre>
     * @param Array $array <p>Исходный массив объектов</p>
     * @param String $field <p>Поле, по которому нужно построить карту</p>
     * @param Callback $preprocess <p>Функция, которой нужно обработать индексы</p>
     */
    public static function index ( $array, $field, $preprocess = null )
    {
        $keys = self::pluck ( $array, $field );
        if ( $preprocess )
            $keys = array_map ( $preprocess, $keys );
        $result = $keys ? array_combine ( $keys, $array ) : array ();

        return $result;
    }

    /**
     * <pre>Возвращает первый элемент массива. Но в отличие от array_shift
     * не изменяет исходный массив и не требует передачи массива по ссылке</pre>
     */
    public static function head ( $array )
    {
        return array_shift ( $array );
    }

    /**
     * <pre>Возвращает последний элемент массива. Но в отличие от array_pop
     * не изменяет исходный массив и не требует передачи массива по ссылке</pre>
     */
    public static function tail ( $array )
    {
        return array_pop ( $array );
    }

    /**
     * <pre>Собирает значения полей массива объектов или массивов в отдельный массив</pre>
     * @param Array $array Массив объектов или массивов
     * @param String $field Название поля
     */
    public static function pluck ( $array, $field )
    {
        $result = array ();
        foreach ( $array as $item )
        {
            if ( is_object ( $item ) && isset ( $item->$field ) )
                $result[] = $item->$field;
            elseif ( is_array ( $item ) && isset ( $item[$field] ) )
                $result[] = $item[$field];
            else
                $result[] = null;
        }

        return $result;
    }

    /**
     * <pre>Преобразует строку в массив, используя запятую как разделитель</pre>
     */
    public static function asArray ( $value )
    {
        if ( is_array ( $value ) )
            return $value;
        else if ( strstr ( $value, ',' ) )
            return explode ( ',', $value );
        else if ( !is_null ( $value ) )
            return array ( $value );
        else
            return array ();
    }

    /**
     * <pre>Преобразовывает массив в список строк, заключенных в кавычки и разделенных запятой</pre>
     * @param Array $list <p>Исходный массив</p>
     * @return String <p>Результирующая строка</p>
     */
    public static function stringList ( $list )
    {
        $values = self::asArray ( $list );
        $strings = array ();
        foreach ( $values as $string )
        {
            $string = SqlTools::escapeString ( $string );
            $strings[] = "'$string'";
        }

        return implode ( ',', $strings );
    }

    /**
     * <pre>Преобразовывает массив в список целых чисел, разделенных запятой</pre>
     * @param Array $list <p>Исходный массив</p>
     * @return String <p>Результирующая строка</p>
     */
    public static function numberList ( $list )
    {
        return implode ( ',', array_map ( 'intval', self::asArray ( $list ) ) );
    }

    /**
     * <pre>Сортирует массив объектов</pre>
     * @param Array $array <p>Исходный массив</p>
     * @param String $field <p>Поле по которому сортировать массив</p>
     * @param String $order <p>Направление сортировки</p>
     * @return Array <p>Отсортированный массив</p>
     */
    public static function sort ( $array, $field, $order )
    {
        $fieldValues = self::pluck ( $array, $field );
        if ( $order == 'DESC' )
            rsort ( $fieldValues );
        else
            sort ( $fieldValues );
        $return = array ();
        foreach ( $fieldValues as $value )
        {
            $selected = self::select ( $array, $field, $value );
            $return = array_merge ( $return, $selected );
        }

        return $return;
    }

    /**
     * <pre>Объединяет два и более массивов<pre>
     * @param Array $first <p>Первый (исходный) массив</p>
     * @param Array $second <p>Последующий массив</p>
     * @return Array
     */
    public static function merge ( $first, $second )
    {
        $args = func_get_args ();
        $res = array_shift ( $args );
        while ( !empty ( $args ) )
        {
            $next = array_shift ( $args );
            foreach ( $next as $key => $arg )
            {
                if ( is_integer ( $key ) )
                    isset ( $res[$key] ) ? $res[] = $arg : $res[$key] = $arg;
                elseif ( is_array ( $arg ) && isset ( $res[$key] ) && is_array ( $res[$key] ) )
                    $res[$key] = self::merge ( $res[$key], $arg );
                else
                    $res[$key] = $arg;
            }
        }
        return $res;
    }
}
