<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PriceUpdater
 *
 * @author kraser
 */
class PriceUpdater extends Component
{
    private $categories;
    private $categoriesByName;
    private $currentCategory;
    private $categoryStack;
    private $productsById;
    private $productsByName;
    private $brandsByName;
    private $nameHash;
    private $priceList;

    public function __construct ()
    {
        //$this->categoriesById = array();
        $this->categoriesByName = array ();
        $this->productsById = array ();
        $this->productsByName = array ();
        $this->readPrice ();
    }

    public function init ( $priceList )
    {
        $this->priceList = $priceList;
    }

    public function Run ()
    {
        foreach ( $this->priceList->categories as $category )
        {
            $this->updateCategory ( $category );
        }
    }

    private function updateCategory ( $category, $parent = null, $level = 0 )
    {
        if ( (!$category->name ) )
        {
            return;
        }

        array_splice ( $this->categoryStack, $level );
        $this->categoryStack[] = $category;
        $names = array_map ( "trim", ArrayTools::pluck ( $this->categoryStack, "name" ) );
        $name = strtolower ( $category->name );
        $key = strtolower ( implode ( " | ", $names ) );
        $visible = (count ( $category->items ) || count ( $category->categories ));

        $referCategory = array_key_exists ( $key, $this->categoriesByName ) ? $this->categoriesByName[$key] : null;
        $this->currentCategory = $referCategory;

        $nav = $this->translate ( $name );
        $query = null;
        $update = array ();
        if ( $referCategory )
        {

            $category->id = $referCategory->id;
            if ( $visible && $referCategory->show == 'N' )
            {
                $update[] = "`show`='Y'";
            }
            else if ( !$visible && $referCategory->show == 'Y' )
            {
                $update[] = "`show`='N'";
            }

            if ( $referCategory->nav != $nav )
            {
                $update[] = "`nav`='$nav'";
            }



            if ( count ( $update ) )
            {
                $updateSet = implode ( ',', $update );
                $query = "UPDATE `prefix_products_topics` SET $updateSet, `modified`=NOW() WHERE `id`=" . $category->id;
                SqlTools::execute ( $query );
            }
        }
        else
        {
            $show = $visible ? 'Y' : 'N';
            $parentId = $parent ? $parent->id : 0;
            $query = "INSERT INTO `prefix_products_topics` (`top`, `name`, `nav`, `show`, `created`)
                VALUES ($parentId, '$name', '$nav', '$show', NOW())";

            $category->id = SqlTools::insert ( $query );
        }

        if ( count ( $category->categories ) )
        {
            foreach ( $category->categories as $subCategory )
            {
                $this->updateCategory ( $subCategory, $category, $level + 1 );
            }
        }
        $this->currentCategory = $referCategory;

        if ( count ( $category->items ) )
        {
            foreach ( $category->items as $item )
            {
                $this->updateItem ( $item, $category );
            }
        }
    }

    private function updateItem ( $item, $category )
    {
        $name = strtolower ( $item->name );
        $key = $this->createKey ( $name, $item->brand );

        if ( array_key_exists ( $key, $this->currentCategory->items ) )
        {
            $referItem = $this->currentCategory->items[$key];
        }
        else if ( array_key_exists ( $name, $this->currentCategory->items ) )
        {
            $referItem = $this->currentCategory->items[$name];
        }
        else
        {
            $referItem = null;
        }

        $nav = $this->translate ( $name );
        $query = null;

        if ( $item->brand )
        {
            $this->updateBrand ( $item );
        }

        if ( $referItem )
        {
            $update = array ();
            if ( $item->price != $referItem->price )
            {
                $update[] = "`price`=$item->price";
            }

            if ( $nav != $referItem->nav )
            {
                $update[] = "`nav`='$nav'";
            }

            if ( $referItem->brand != $item->brand )
            {
                $update[] = "`brand`='$item->brand'";
            }

            if ( $item->anons != $referItem->anons )
            {
                $update[] = "`anons`='$item->anons'";
            }

            if ( $item->unit != $referItem->unit )
            {
                $update[] = "`unit`='$item->unit'";
            }

            if ( count ( $update ) )
            {
                $updateSet = implode ( ",", $update );
                $query = "UPDATE `prefix_products` SET $updateSet, `modified`=NOW() WHERE `id`=$referItem->id";
                SqlTools::execute ( $query );
            }
        }
        else
        {
            $brandId = $item->brand ? : 0;
            $query = "INSERT INTO `prefix_products` (`top`, `name`, `anons`, `nav`, `brand`, `unit`, `price`, `show`, `created`)
                VALUES ($category->id, '$name', '$item->anons', '$nav', $brandId, '$item->unit', $item->price, 'Y', NOW())";
            SqlTools::insert ( $query );
        }
    }

    private function updateBrand ( $item )
    {
        $nav = $this->translate ( $item->brand );
        $referBrand = array_key_exists ( $nav, $this->brandsByName ) ? $this->brandsByName[$nav] : null;
        if ( $referBrand )
        {
            $item->brand = $referBrand->id;
        }
        else
        {
            $name = $item->brand;
            //$nav = $this->translate ( $name );
            $query = "INSERT INTO `prefix_products_brands` (`name`,`nav`, `show`, `deleted`, `created`) VALUES ('$name', '$nav', 'Y', 'N', NOW())";
            $brand = new stdClass;
            $brand->id = SqlTools::insert ( $query );
            $item->brand = $brand->id;
            $brand->nav = $nav;
            $brand->name = $name;
            $this->brandsByName[$nav] = $brand;
        }
    }

    private function translate ( $string )
    {
        $retVal = translate ( $string );
        $retVal = trim ( preg_replace ( '/[^-A-Za-z0-9\s]/', '', $retVal ) );
        $retVal = strtolower ( preg_replace ( '/[-\s]+/', '-', $retVal ) );
        return $retVal;
    }

    private function readPrice ()
    {
        $query = "SELECT * FROM `prefix_products_topics`";
        $categoriesById = SqlTools::selectObjects ( $query, "Category", "id" );
        $categoriesTree = array ();
        foreach ( $categoriesById as $category )
        {
            if ( $category->top == 0 )
            {
                $categoriesTree[$category->id] = $category;
            }
            else
            {
                $parent = $categoriesById[$category->top];
                $parent->categories[$category->id] = $category;
            }
        }
        foreach ( $categoriesTree as $category )
        {
            $name = strtolower ( trim ( $category->name ) );
            $this->categoriesByName[$name] = $category;
            $this->categoryStack[0] = $category;
            $this->createNameHash ( $category );
        }

        $query = "SELECT * FROM `prefix_products`";
        $this->productsById = SqlTools::selectObjects ( $query, "Product", "id" );

        $query = "SELECT * FROM `prefix_products_brands`";
        $this->brandsByName = SqlTools::selectObjects ( $query, null, "nav" );
        foreach ( $this->productsById as $product )
        {
            $brand = array_shift ( ArrayTools::select ( $this->brandsByName, "id", $product->id ) );
            $brandName = $brand && $brand->name ? $brand->name : "";
            $key = $this->createKey ( $product->name, $brandName );
            //$this->productsByName[$key] = $product;

            if ( array_key_exists ( $product->top, $categoriesById ) )
            {
                $category = $categoriesById[$product->top];
            }
            else
            {
                continue;
            }

            $category->items[$key] = $product;
        }
    }

    private function createNameHash ( $category, $level = 0 )
    {
        array_splice ( $this->categoryStack, ++$level );
        if ( count ( $category->categories ) )
        {
            foreach ( $category->categories as $subCategory )
            {
                $this->categoryStack[$level] = $subCategory;
                $names = array_map ( "trim", ArrayTools::pluck ( $this->categoryStack, "name" ) );
                $name = strtolower ( implode ( " | ", $names ) );
                $this->categoriesByName[$name] = $subCategory;

                $this->createNameHash ( $subCategory, $level );
            }
        }
    }

    private function createKey ( $itemName, $brandName )
    {
        $brandName = $brandName ? : "";
        return strtolower ( trim ( $itemName ) . trim ( $brandName ) );
    }
}
