<?php

/**
 * Класс реализующий режим Ajax
 */
class Ajax extends CmsApplication
{

    /**
     * Конструктор режима Ajax
     * Сканирует директорию LIBS и подгружает библиотеки,
     * не оформленные в виде классов
     */
    public function __construct ()
    {
        parent::__construct ();
        //Загрузка библиотек сайта
        $lib = scandir ( LIBS, 1 );
        foreach ( $lib as $file )
        {
            if ( substr ( $file, -3, 3 ) == 'php' )
            {
                require_once( LIBS . DS . $file);
            }
        }
    }

    public function Run ()
    {
        //Если запрос пришел аяксом
        if ( !empty ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
        {
            $modes = array ( "admin", "install" );
            $parser = Starter::app ()->urlManager;

            $startPart = $parser->pullUrlPart ();

            /* В action формы ставим Content/Form/<idForm> и всё должно срабатывать
              if(isset($_POST['ajax_form']))
              {
              $componentName = 'Content';
              $methodName = 'form';
              $rc = new ReflectionClass ( $componentName );
              $component = $rc->newInstance ();
              $result = $component->$methodName($_POST['form_id'], true);
              }
              else */
            if ( in_array ( $startPart, $modes ) )
            {
                //Работаем с классами мод
            }
            else
            {
                $componentName = ucfirst ( $startPart );
                $methodName = $parser->pullUnshiftUrlPart ();
                if ( method_exists ( $componentName, $methodName ) )
                {
                    $methodName = $parser->pullUrlPart ();
                    $reflectMethod = new ReflectionMethod ( $componentName, $methodName );
                    $result = $reflectMethod->invoke ( Starter::app ()->getModule ( $componentName ), $parser->getUriParts (), true );
                }
                else
                {
                    $component = Starter::app ()->getModule($componentName);
                    $result = $component->Run ();
                }
            }

            $accept = filter_input ( INPUT_SERVER, 'HTTP_ACCEPT' );
            if ( stripos ( $accept, "application/json" ) !== false )
            {
                jsonHeader ();
                sendJSON ( $result );
            }
            elseif ( stripos ( $accept, "application/xml" ) !== false )
            {
                xmlHeader ();
                //todo Создать из объекта xml и отправить его
            }
            else
            {
                htmlHeader ();
                echo $result;
            }


            //$module = $parser->getUriComponent();
            //$method = $parser->getUriMethod();

            /*
              if ( class_exists( $module ) )
              {
              $moduleObj = ComponentFactory::getComponent( $module );
              if ( method_exists( $moduleObj, $method ) )
              echo $moduleObj->$method();//Финальный вывод модуля здесь!
              else
              error( 'В модуле ' . $module . ' не задан метод ' . $method );
              }
              else
              {
              error( 'Модуль ' . $module . ' не найден!' );
              }
             */
        }
    }
}
