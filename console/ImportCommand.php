<?php
class ImportCommand extends CmsComponent
{
    public function __construct ($args)
    {
        parent::__construct ();

    }

    public function Run ()
    {

    }
}
////require_once ( "config.php" );
//require_once ( 'define.php' );
//ini_set ( 'default_charset', _CHARSET );
//require_once ( SYS . DS . 'loading.php' );
//require_once (TOOLS.DS."SqlTools.php");
////require_once (TOOLS.DS."SqlTools.php");
//
//$topics = SqlTools::selectObjects ( "SELECT * FROM `prefix_products_topics`" );
//$tree = [];
//$list = [];
//$byName = [];
//foreach ( $topics as $topic )
//{
//    $key = md5($topic->name);
//    $byName[$key] = $topic;
//    $list[$topic->id] = $topic;
//    if($topic->top == 0)
//    {
//        $tree[$topic->id] = $topic;
//    }
//    else
//    {
//        $parent = $list[$topic->top];
//        $parent->subCat[$topic->id] = $topic;
//    }
//}
//
//$products = ArrayTools::index(SqlTools::selectObject("SELECT * FROM `prefix_products`"), "shortName" );
//
//$fileName = './import/offers.xml';
//$content = file_get_contents ( $fileName );
//$xml = simplexml_load_string ( $content ) or die ( "Error: Cannot create object" );
//
//foreach($xml->offer as $offer)
//{
//    $searchKey = md5 ( $offer->object );
//
//    $object = SqlTools::selectObject("SELECT * FROM `prefix_products_topics` WHERE `top`='0' AND `name`='".(string)$offer->object."'");
//
//    if(!$object)
//    {
//        $obectId = SqlTools::insert("INSERT INTO `prefix_products_topics`(`top`, `name`) VALUES (0, '".(string)$offer->object."')");
//        SqlTools::execute("UPDATE `prefix_products_topics` SET `nav`='object".$obectId."' WHERE `id`='".$obectId."'");
//    }
//    else
//    {
//        $obectId = $object->id;
//    }
//
//    $house = SqlTools::selectObject("SELECT * FROM `prefix_products_topics` WHERE `top`='".(int)$obectId."' AND `name`='".(string)$offer->house."'");
//
//    if(!$house)
//    {
//        $houseId = SqlTools::insert("INSERT INTO `prefix_products_topics`(`top`, `name`) VALUES (".(int)$obectId.", '".(string)$offer->house."')");
//    }
//    else
//    {
//        $houseId = $house->id;
//    }
//
//    $result = SqlTools::selectObject("SELECT * FROM `prefix_products` WHERE `shortName`='".(string)$offer['articul']."'");
//    if(!$result)
//    {
//        $resultId = SqlTools::insert("INSERT INTO `prefix_products`(`top`, `name`, `shortName`, `price`) VALUES ('".$houseId."','".(string)$offer->type." ".(string)$offer->nom."','".(string)$offer['articul']."','".(string)$offer->totalPrice."')");
//    }
//    else
//    {
//        SqlTools::execute("UPDATE `prefix_products` SET `price`='".(string)$offer->totalPrice."' WHERE `id`='".$result->id."'");
//        $resultId = $result->id;
//    }
//
//    SqlTools::execute("DELETE FROM `bm_tags_values` WHERE moduleId='".$resultId."'");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '1', '".(string)$offer->block."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '2', '".(string)$offer->type."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '3', '".(string)$offer->nom."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '4', '".(string)$offer->rooms."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '5', '".(string)$offer->floor."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '6', '".(string)$offer->totalArea."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '7', '".(string)$offer->livingArea."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '8', '".(string)$offer->kitchenArea."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '9', '".(string)$offer->decoration."')");
//    SqlTools::insert("INSERT INTO `prefix_tags_values`(`moduleId`, `tagId`, `value`) VALUES ('".$resultId."', '10', '".(string)$offer->pricePerSq."')");
//}
//
///*
//<offer articul="19426">
//		<object>м/р Тулинское</object>
//		<house>Тулинское 9 (стр.),</house>
//		<block>5</block>
//		<type>Квартира</type>
//		<nom>170</nom>
//		<rooms>1а</rooms>
//		<floor>14</floor>
//		<totalArea>31,24</totalArea>
//		<livingArea>19,88</livingArea>
//		<kitchenArea>0</kitchenArea>
//		<decoration>Самоотделка</decoration>
//		<pricePerSq>42000.00</pricePerSq>
//		<totalPrice>1386840.00</totalPrice>
//	</offer>
// *
// *
// */
///*
//| id       | int(8) unsigned | NO   | PRI | NULL    | auto_increment |
//| top      | int(8) unsigned | NO   | MUL | 0       |                |
//| order    | int(11)         | NO   | MUL | NULL    |                |
//| name     | varchar(255)    | NO   |     |         |                |
//| nav      | varchar(100)    | NO   |     |         |                |
//| show     | enum('Y','N')   | NO   | MUL | Y       |                |
//| deleted  | enum('Y','N')   | NO   | MUL | N       |                |
//| created  | datetime        | NO   |     | NULL    |                |
//| modified | datetime        | NO   |     | NULL    |                |
//| text     | text            | NO   |     | NULL    |                |
//| types    | longtext        | NO   |     | NULL    |                |
//| cases    | text            | NO   |     | NULL    |                |
//| rate     | int(11)         | NO   | MUL | NULL    |                |
//| isModel  | enum('Y','N')   | NO   |     | N       |                |
// *
// */
///*
//| id           | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
//| top          | int(11)          | NO   | MUL | NULL    |                |
//| order        | int(11)          | NO   |     | NULL    |                |
//| name         | varchar(255)     | NO   |     | NULL    |                |
//| shortName    | varchar(64)      | NO   |     | NULL    |                |
//| nav          | varchar(100)     | NO   |     | NULL    |                |
//| brand        | int(11)          | NO   |     | NULL    |                |
//| price        | float(11,2)      | NO   |     | NULL    |                |
//| currency     | int(11)          | NO   |     | NULL    |                |
//| unit         | varchar(10)      | YES  |     |         |                |
//| date         | datetime         | NO   |     | NULL    |                |
//| show         | enum('Y','N')    | NO   | MUL | Y       |                |
//| deleted      | enum('Y','N')    | NO   | MUL | N       |                |
//| created      | datetime         | NO   |     | NULL    |                |
//| modified     | datetime         | NO   |     | NULL    |                |
//| anons        | text             | NO   |     | NULL    |                |
//| text         | text             | NO   |     | NULL    |                |
//| types        | longtext         | NO   |     | NULL    |                |
//| is_action    | enum('Y','N')    | NO   |     | N       |                |
//| is_featured  | enum('Y','N')    | NO   |     | N       |                |
//| is_lider     | enum('Y','N')    | NO   |     | N       |                |
//| is_exist     | enum('Y','N')    | NO   |     | Y       |                |
//| availability | text             | NO   |     | NULL    |                |
//| relations    | varchar(255)     | NO   |     | NULL    |                |
//| rate         | int(11)          | NO   |     | NULL    |                |
//| discount     | int(11)          | NO   |     | NULL    |                |
//| noIndex      | enum('Y','N')    | NO   |     | N       |                |
// */