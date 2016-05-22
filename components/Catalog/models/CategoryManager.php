<?php
/**
 * Description of CategoryManager
 *
 * @author kraser
 */
class CategoryManager
{
    /**
     * <p>Возвращает массив категорий товаров</p>
     * @param Array $params <p>Массив параметров для выбора категоий</p>
     * @return type
     */
    public function findCategories ( $params = null )
    {
        if ( !$params )
            return [];

        $conditions = [];
        $orderBy = [];
        foreach ( $params as $field => $value )
        {
            if ( !$value )
                continue;

            switch ( $field )
            {
                case "id":
                    $conditions[] = "t.`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "parentId":
                    $conditions[] = "t.`top` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "order":
                    $conditions[] = "t.`order` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "title":
                    $conditions[] = "t.`name` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
                    break;
                case "link":
                    $conditions[] = "t.`nave` IN (" . ArrayTools::stringList ( $value ) . ")";
                    break;
                case "show":
                    $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                    $conditions[] = "t.`show`='$fieldValue'";
                    break;
                case "deleted":
                    $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                    $conditions[] = "t.`deleted`='$fieldValue'";
                    break;
//                case "isModel":
//                    $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
//                    $conditions[] = "t.`isModel`='$fieldValue'";
//                    break;
                case "created":
                    $conditions[] = "t.`created`='" . SqlTools::escapeString ( $value ) . "'";
                    break;
                case "modified":
                    $conditions[] = "t.`modified`='" . SqlTools::escapeString ( $value ) . "'";
                    break;
                case "text":
                    $conditions[] = "t.`text` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
                    break;
//                case "types":
//                    $conditions[] = "t.`types` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
//                    break;
                case "short":
                    $conditions[] = "t.`cases` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
                    break;
                case "rate":
                    $conditions[] = "t.`rate`=" . intval ( $value );
                    break;
                case "orderBy":
                    $orderBy[] = $value;
                    break;
                default:
            }
        }
        $whereClause = count ( $conditions ) ? "WHERE " . implode ( " AND ", $conditions ) : "";
        $orderClause = count ( $orderBy ) ? "ORDER BY " . implode ( ",", $orderBy ) : "ORDER BY `order`";
        $query = "
            SELECT
                t.`id` AS id,
                t.`top` AS parentId,
                t.`order` AS 'order',
                t.`name` AS title,
                t.`text` AS text,
                t.`cases` AS short,
                t.`isModel` AS isModel,
                t.`rate` AS rate,
                t.`nav` AS link,
                t.`show` AS view,
                t.`deleted` AS deleted,
                t.`created` AS createDate,
                t.`modified` AS modifyDate,
                i.`src` AS image
            FROM `prefix_products_topics` t
            LEFT JOIN `prefix_images` i ON (i.`module`='Topic' AND i.`module_id`=t.`id` AND i.`main`='Y')
            $whereClause
            $orderClause
            ";
        $categories = SqlTools::selectObjects ( $query, "Category", "id" );
        if ( !count ( $categories ) )
            return array ();

        $ids = ArrayTools::numberList ( ArrayTools::pluck ( $categories, "id" ) );
        $productsInCategory = SqlTools::selectObjects ( "SELECT `top`, count(`top`) AS `count` FROM `prefix_products` WHERE `top` IN ($ids) AND `deleted`='N' AND `show`='Y' GROUP BY `top`", null, "top" );
        foreach ( $categories as $category )
        {
            $category->quantity = array_key_exists ( $category->id, $productsInCategory ) ? $productsInCategory[$category->id] : 0;
            //$category->link = $this->Link ( $category->id );
            $category->image = $category->image ? : "/data/moduleImages/Topic/no.png";
//            $product->image = img ()->GetMainImage ( 'Catalog', $product->id );
//            $product->images = img ()->GetImages ( 'Catalog', $product->id );
        }

        return $categories;
    }

    public function getCatalogTree ( $id = null, $branchesOnly = false )
    {
        $params = array
        (
            "deleted" => 'N',
            "show" => 'Y'
        );

        $categories = $this->findCategories ( $params );

        $categoriesTree = array ();
        foreach ( $categories as $category )
        {
            if ( $category->parentId == 0 )
                $categoriesTree[$category->id] = $category;
            else
            {
                if ( !array_key_exists ( $category->parentId, $categories ) )
                    continue;

                $parentCategory = $categories[$category->parentId];
                $parentCategory->subCategories[$category->id] = $category;
            }
        }

        if ( $id && is_numeric ( $id ) )
            $returnedTree = array_key_exists ( $id, $categories ) ? $categories[$id] : null;
        else
        {
            $returnedTree = new Category();
            $returnedTree->short = "";
            $returnedTree->text = "";
            $returnedTree->subCategories = $categoriesTree;
        }

        if ( $branchesOnly )
            return $returnedTree ? $returnedTree->subCategories : [];
        else
            return $returnedTree;
    }
}

class Category
{
    public $id;
    public $parentId;
    public $order;
    public $title;
    public $text;
    public $short;
    public $rate;
    public $isModel;
    public $image;
    public $subCategories = [];
    public $quantity;
    public $products = [];
    public $link;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;

}