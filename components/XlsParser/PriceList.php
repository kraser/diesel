<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PriceList
 *
 * @author kraser
 */
class PriceList
{
    var $supplier;      // Кодовое название поставщика
    var $time;          // Дата-время скачки прайса
    var $valuta;        // Валюта прайса
    var $dollarCourse; // Курс доллара (если есть в прайсе)
    var $categories;    // Список категорий верхнего уровня
    var $categoryStack; // Стек категорий для парсинга экселовских прайсов
    var $codeHash = array ();      // Список позиций прайса, индексированный по коду
    var $modelHash = array ();     // Список позиций прайса, индексированный по модели (альтернативный код позиции)
    var $nameHash = array ();      // Список позиций прайса, индексированный по названию
    var $isSpecial;     // Признак наличия в прайсе только позиций со спецценами

    function __construct ( $supplier = null, $valuta = null, $categories = null )
    {
        $this->supplier = $supplier;
        $this->valuta = $valuta;
        $this->categories = $categories ? $categories : array ();
        $this->time = date ( 'Y-m-d H:i' );
        $this->keepCategory = new Category;
        $this->categories[] = $this->keepCategory;
    }

    function getCurrentCategoryFullName ()
    {
        if ( $this->categoryStack )
        {
            return join ( ' | ', array_pluck ( $this->categoryStack, 'name' ) );
        }
        else
        {
            return DEFAULT_CATEGORY;
        }
    }

    function getCurrentCategory ()
    {
        if ( $this->categoryStack )
        {
            return $this->categoryStack[count ( $this->categoryStack ) - 1];
        }
        else
        {
            return $this->setCurrentCategory ( "default" );
        }
    }

    function setCurrentCategory ( $name, $level = 0, $parent = null )
    {
        if ( !$this->categoryStack )
            $this->categoryStack = array ();
        if ( !$this->categories )
            $this->categories = array ();

        if ( !is_null ( $parent ) )
        {
            while ( $this->categoryStack && $this->categoryStack[count ( $this->categoryStack ) - 1]->code != $parent )
            {
                array_splice ( $this->categoryStack, count ( $this->categoryStack ) - 1 );
            }
        }
        else
        {
            if ( $level < 0 )
                $level = 0;;

            if ( count ( $this->categoryStack ) > $level )
            {
                array_splice ( $this->categoryStack, $level );
            }

            while ( count ( $this->categoryStack ) < $level )
            {
                $category = new PriceCategory ();
                if ( $this->categoryStack )
                {
                    $this->categoryStack[count ( $this->categoryStack ) - 1]->categories[] = $category;
                }
                else
                {
                    $this->categories[$category->name] = $category;
                }
                $this->categoryStack[] = $category;
            }
        }

        if ( $name )
        {
            //Выбираем ближайший родительский элемент
            if ( $this->categoryStack )
            {
                $parent = $this->categoryStack[count ( $this->categoryStack ) - 1];
            }
            else
            {
                $parent = $this;
            }

            if ( is_array ( $parent->categories ) AND array_key_exists ( $name, $parent->categories ) )
            {
                $category = $parent->categories[$name]; //Если в родителе есть категория с таким именем выбираем её как текущую
            }
            else
            {
                $category = new Category();
                $category->name = $name;
            }
            $this->categoryStack[] = $category; //и вносим категорию в стек
            $parent->categories[$category->name] = $category;
        }
        else
        {
            $this->categoryStack[] = $category = $this->keepCategory;
        }

        return $category;
    }

    /**
     * <p>Добавление позиции в прайс</p>
     */
    function addItem ( $item )
    {
        if ( !$item->name )
            return;

        $currentCategory = $this->getCurrentCategory ();
        $name = strtolower ( $item->name ) . ($item->brand ? strtolower ( $item->brand ) : "");
        if ( array_key_exists ( $name, $currentCategory->items ) )
        {
            $currentItem = $currentCategory->items[$name];
        }
        else
        {
            $currentItem = null;
        }

        if ( $currentItem )
        {
            return;
            $currentItem->price = $item->price;
        }
        else
        {
            $key = strtolower ( $item->name ) . ($item->brand ? strtolower ( $item->brand ) : "");
            $this->nameHash[$key] = $item;

            $currentCategory->items[$name] = $item;
        }
    }

    function getItemLike ( $item )
    {
        $name = strtolower ( $item->name ) . ($item->brand ? strtolower ( $item->brand ) : "");
        if ( array_key_exists ( $name, $this->nameHash ) )
        {
            return $this->nameHash [$name];
        }
        else
        {
            return null;
        }
    }

    function isEmpty ()
    {
        return count ( $this->nameHash ); // OR count($this->codeHash));
    }

    /** Возвращает список всех позиций включенных в прайс
     *
     * @param PriceCategory $inputCategory
     * @return array
     */
    function getItemsList ( $inputCategory = null )
    {
        $categories = array ();

        if ( $inputCategory )
        {
            $categories = $inputCategory->categories;
            $items = $inputCategory->items ? $inputCategory->items : array ();
        }
        else
        {
            $categories = $this->categories;
            $items = array ();
        }

        if ( !sizeof ( $categories ) )
        {
            return $items;
        }

        foreach ( $categories as $category )
        {
            $moreItems = $this->getItemsList ( $category );
            $items = array_merge ( $items, $moreItems );
        }

        return $items;
    }

    function makeHash ()
    {
        $items = $this->getItemsList ();
        foreach ( $items as $item )
        {
            if ( isValidValue ( $item->code ) )
            {
                $this->codeHash[strtolower ( $item->code )] = $item;
            }

            if ( isValidValue ( $item->model ) )
            {
                $this->modelHash[strtolower ( $item->model )] = $item;
            }

            if ( isValidValue ( $item->name ) )
            {
                $this->nameHash[strtolower ( $item->name )] = $item;
            }
        }
    }
}
