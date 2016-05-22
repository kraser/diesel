<?php

/**
 * Преобразование произвольного объекта в строку xml
 * @author kraser <kravalsergey@yandex.ru>
 *
 *
 * ! В качестве названия тега используется название класса.
 * ! Все поля, значения которых являются массивами, разворачиваются как дочерние элементы xml
 *
 * @param $object   Корневой объект или массив для преобразования в xml
 * @param $tagName  Имя тега для корневого объекта или обёртки для массива
 * @param $plain    Выводить/скрывать в xml дочерние объекты
 *                  Если $plain == true, то выводится только корневой объект и его аттрибуты
 *                  или объекты массива и их аттрибуты, но не дочерние объекты
 * @param $packed   Выводить/скрывать обёртку для дочерних массивов - полей родительсвого объекта.
 *                  Например есть класс<code><pre>
 *                  class Parent {
 *                      var $childen = array (); // Массив объектов Child
 *                  }
 *                  </pre></code>
 *                  При $packed == false на выходе будет
  <code><pre>
 *                  &lt;Parent&gt;
 *                      &lt;children&gt;
 *                          &lt;Child/&gt;
 *                  </pre></code>
 *                  А если $packed == true, то тег <children> будет пропущен<code><pre>
 *                  &lt;Parent&gt;
 *                      &lt;Child/&gt;
 *                  </pre></code>
 * @return  xml в виде одной строки
 */
class CmsXmlWriter
{
    var $stream;
    var $plain;     // Не выводить дочерние объекты
    var $packed;    // Не обрамлять дочерние коллекции объектов тегом с именем члена, содержащего коллекцию
    var $result;    // Буфер для записи текста, если в конструкторе не был задан поток вывода
    var $iszero;    // Выводить нуливые значения
    private static $chars;
    private static $charEscapes;

    function __construct ( $output = null )
    {
        if ( is_resource ( $output ) )
        {
            $this->stream = $output;
        }
        else if ( is_string ( $output ) )
        {
            $this->stream = fopen ( $output, 'wt' );
        }
        else
        {
            $this->stream = null;
        }

        $this->initialize ();
    }

    function initialize ()
    {
        if ( is_resource ( $this->stream ) )
        {
            $this->write ( "<?xml version='1.0' encoding='" . _CHARSET . "'?>" );
        }
        else if ( is_string ( $this->stream ) )
        {
            $this->write ( "<?xml version='1.0' encoding='" . _CHARSET . "'?>" );
        }
        else
        {
            $this->result = '';
        }
    }

    function close ()
    {
        if ( is_resource ( $this->stream ) )
        {
            fclose ( $this->stream );
        }
    }

    function writeText ( $text )
    {
        if ( is_resource ( $this->stream ) )
        {
            fwrite ( $this->stream, $text );
        }
        else if ( is_string ( $this->stream ) )
        {
            @mkdir ( dirname ( $this->stream ) );
            $file = fopen ( $this->stream, 'at' );
            if ( $file )
            {
                fwrite ( $file, $text );
                fclose ( $file );
            }
        }
        else
        {
            $this->result .= $text;
        }
    }

    function write ( $object, $tagName = null )
    {
        if ( is_string ( $object ) )
        {
            $this->writeText ( $object );
        }
        else if ( is_array ( $object ) )
        {
            $this->writeArray ( $object, $tagName );
        }
        else if ( is_object ( $object ) )
        {
            $this->writeObject ( $object, $tagName );
        }
        else
        {
            if ( !$tagName )
            {
                $tagName = 'null';
            }
            $this->writeText ( "<$tagName/>" );
        }

        return $this->result;
    }

    function writeArray ( $array, $tagName = null )
    {
        if ( $tagName )
        {
            $this->writeText ( "<$tagName>" );
        }
        foreach ( $array as $object )
        {
            $this->write ( $object );
        }
        if ( $tagName )
        {
            $this->writeText ( "</$tagName>" );
        }
    }

    function writeObject ( $object, $tagName = null )
    {
        if ( !$tagName )
        {
            $tagName = get_class ( $object );
        }

        $fields = get_object_vars ( $object );
        if ( $fields )
        {
            $classFields = get_class_vars ( get_class ( $object ) );
            $children = array ();
            $this->writeText ( "<$tagName" );

            foreach ( $fields as $field => $value )
            {
                if ( !array_key_exists ( $field, $classFields ) )
                {
                    continue;
                }

                if ( ($value) || ($this->iszero) )
                {
                    if ( is_array ( $value ) || is_object ( $value ) )
                    {
                        if ( !$this->plain )
                        {
                            $children[$field] = $value;
                        }
                    }
                    else
                    {
                        // заменяем все переносы строки и табуляции на пробелы
                        $value = preg_replace ( '/[\n\t]/', ' ', $value );
                        if ( ($value) || ($this->iszero) )
                        {
                            $this->writeText ( " $field='" . self::escapeXml ( $value ) . "'" );
                        }
                    }
                }
            }

            if ( $children )
            {
                $this->writeText ( ">" );
                foreach ( $children as $field => $child )
                {
                    $this->write ( $child, ($this->packed ? null : $field ) );
                }
                $this->writeText ( "</$tagName>" );
            }
            else
            {
                $this->writeText ( "/>" );
            }
        }
        else
        {
            $this->writeText ( "<$tagName/>" );
        }
    }

    /** Защита от спецсимволов для формирования текста XML
     *
     * В дополнение к стандартному набору символов (&, <, >, кавычки),
     * защищаются также непечатные символы из диапазона 0x00 - 0x1F.
     */
    static function escapeXml ( $text )
    {
        if ( !self::$chars )
        {
            self::$chars = array ( '&', '"', "'", '<', '>' );
            self::$charEscapes = array ( '&amp;', '&quot;', '&#039;', '&lt;', '&gt;' );

            for ( $i = 0; $i < 0x20; ++$i )
            {
                self::$chars[] = chr ( $i );
                self::$charEscapes[] = sprintf ( "&amp;#%02d;", $i );
            }
        }

        $text = str_replace ( self::$chars, self::$charEscapes, $text );

        return $text;
    }
}
