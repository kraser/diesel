<?php

require_once 'Spreadsheet/Excel/Writer.php';

class PriceWriter
{

    protected $workbook;
    protected $worksheet;
    protected $styles;
    protected $fileName;
    protected $sheetName;
    protected $rowNum;
    protected $colNum;
    protected $fieldNames;
    protected $level;
    protected $categoryStack;
    protected $levelMax;
    protected $columns;

    public function __construct( $fileName, $sheetName, $header=null )
    {
        $this->fileName = $fileName;
        $this->sheetName = $sheetName;
        $pathName = $fileName;
        $this->workbook = new Spreadsheet_Excel_Writer( $pathName );
        $this->workbook->setVersion( 8 );

        $this->styles->header =
                $this->workbook->addFormat( array(
            'Border' => 1,
            'Size' => 10,
            'Align' => 'center',
            'Bold' => 1
                ) );

        $this->styles->category =
                $this->workbook->addFormat( array(
            'Border' => 1,
            'Size' => 10,
            'Align' => 'left',
            'Bold' => 1
                ) );

        $this->worksheet = $this->workbook->addWorksheet( $sheetName );
        $this->worksheet->setInputEncoding( 'UTF8' );
        if(!$header)
        {
            $itemObjectDescription = ItemObjectCore::getBaseDescription();
            $columns = array_pluck( $itemObjectDescription, 'name' );
            $this->fieldNames = array_pluck( $itemObjectDescription, 'type' );
        }
        else
        {
            $this->columns = $header;
            $columns = $header;
        }

        $this->worksheet->setRow( 1, 15 );
        $columnNum = 0;
        foreach ( $columns as $column => $title )
        {
            $this->worksheet->write( 1, $columnNum, $title, $this->styles->header );
            $columnNum++;
        }

        $this->rowNum = 2;
        $this->colNum = 0;
        $this->level = 0;
        $this->levelMax = 1;
    }

    public function write($object)
    {
        if(  is_a( $object, "PriceList" ))
                $this->writePrice ( $object);
                elseif ( is_array( $object))
            {
                $this->writeArray($object);
            }


        $this->workbook->close();

        //return new PriceFile( $this->fileName, $GLOBALS['configuration']['folders']['PRICE_WORK_FOLDER'] . '/' . $this->fileName );
    }

    public function writePrice( $price )
    {
        foreach ( $price->categories as $category )
        {
            $this->writeCategory( $category );
        }

        $this->worksheet->write( 0, 0, "Максимальный уровень категорий:" . $this->levelMax );


    }

    public function writeCategory( $category )
    {
        $this->pushCategoryStack( $category );
        $prefix = $this->level. ". ";
        $this->worksheet->write( $this->rowNum, 0, $prefix . $this->categoryStack[$this->level]->name, $this->styles->category );

        $this->rowNum++;

        if ( count( $category->items ) )
        {
            $this->writeItems( $category->items );
        }

        if ( count( $category->categories ) )
        {
            $this->level++;
            foreach ( $category->categories as $subCategory )
            {
                $this->writeCategory( $subCategory );
            }
            $this->level--;
        }
    }

    protected function pushCategoryStack( $category )
    {
        $this->categoryStack[$this->level] = $category;

        if ( $this->level >= $this->levelMax )
            $this->levelMax = $this->level + 1;

        $this->categoryStack = array_slice( $this->categoryStack, 0, $this->level + 1 );
    }

    protected function writeItems( $items )
    {
        $rc = new ReflectionClass( "Product" );
        $props = $rc->getProperties();

        foreach ( $items as $item )
        {
            $fields = array_keys($this->columns);
            foreach ( $fields as $colNum => $propertyName )
            {
                $prop = $rc->getProperty( $propertyName );
                $value = $prop->getValue( $item );

                $this->worksheet->write( $this->rowNum, $colNum, $value );
            }

            $this->rowNum++;
        }
    }

    protected function writeArray( $items )
    {
        foreach ( $items as $item )
        {
            foreach ( $item as $colNum => $value )
            {
                $this->worksheet->write( $this->rowNum, $colNum, $value );
            }

            $this->rowNum++;
        }
    }

}
