<?php
/**
 * Description of XlsParser
 *
 * @author kraser
 */
class XlsParser extends Component implements SplSubject
{
    private $priceList;
    private $observers;

    private $pricePath;
    private $priceName;
    private $xlsEngine;
    private $sheetName;
    private $sheet;
    private $headers;
    private $header;
    private $firstRow;
    private $currentRow;

    private $success;

    const PARSE_SHEET = "PARSE_SHEET";  //завершен парсинг листа
    const PARSE_ERROR = "PARSE_ERROR";  //ошибка парсинга листа
    const PARSE_INIT = "PARSE_INIT";    //инициализация парсера завершена
    const PARSE_FILE = "PARSE_FILE";    //завершен парсинг файла
    const PARSE_MSG = "PARSE_MSG";      //дополнительное сообщение о процессе парсинга

    public function __construct ( $pricePath = null, $priceName = "price.xls" )
    {
        $this->priceList = null;
        $this->observers = new SplObjectStorage();
        $this->pricePath = $pricePath ? : DOCROOT . DS . "data";
        $this->priceName = $priceName;
        $this->header = null;
        $this->firstRow = 0;
        $this->currentRow = 0;
        $readerType = null;
        $this->xlsEngine = new WorkBook ( $this->pricePath . DS . $this->priceName );
        $this->notify ( XlsParser::PARSE_INIT );
    }

    public function init ( $headers )
    {
        $this->headers = $headers;
    }

    public function Run ()
    {
        $this->priceList = new PriceList();
        $sheetCount = $this->xlsEngine->getSheetCount ();
        for ( $sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++ )
        {
            $sheet = $this->xlsEngine->getSheet ( $sheetIndex );
            $this->setSheet ( $sheet );

            if ( $this->isEmptySheet () )
            {
                continue;
            }

            if ( !$this->lookForHeader () )
            {
                continue;
            }

            $this->parseSheet ();
            /*
              if ( !self::$header )
              continue;

              $this->priceList->setCurrentCategory( 0, DEFAULT_CATEGORY );
              $this->parseSheet();

              if ( !self::$success )
              {
              $msg = "Формат листа '" . self::$sheetName . "' в файле '" . self::$dataSourceName . "' не определён!";
              self::$state = (self::$state) ? self::$state : array( "status" => SheetInfo::STATUS_ERROR, 'error' => $msg );
              $this->notify( PriceParser::PARSE_SHEET );
              continue;
              }

              self::$state = array( "status" => SheetInfo::STATUS_OK );
              $this->notify( PriceParser::PARSE_SHEET );
             */
        }
        $this->notify ( XlsParser::PARSE_FILE );
        $this->xlsEngine = null;
    }

    private function setSheet ( $sheet )
    {
        $title = $sheet->getTitle ();
        $this->sheetName = $title;
        $this->sheet = $sheet;
    }

    public function isEmptySheet ()
    {
        if ( $this->sheet->isEmpty () )
        {
            //self::$state = array( "status" => SheetInfo::STATUS_EMPTY, 'error' => 'Лист пустой' );
            //$this->notify( PriceParser::PARSE_SHEET );
            return true;
        }
        else
        {
            return false;
        }
    }

    private function lookForHeader ()
    {
        $hashes = array ();
        foreach ( $this->headers as $headerData )
        {
            $firstRow = $headerData['first'];
            $header = $headerData['header'];
            $referHash = $headerData['hash'];
            if ( array_key_exists ( $firstRow, $hashes ) )
            {
                $hash = $hashes[$firstRow];
            }
            else
            {
                $hash = $this->getHash ( $firstRow );
                $hashes[$firstRow] = $hash;
            }

            if ( $referHash == $hash )
            {
                $this->header = $header;
                $this->firstRow = $firstRow;

                return true;
            }
        }

        return false;
    }

    private function getHash ( $rowNum )
    {
        $columns = $this->sheet->getColumnCount ();
        $hash = array ();
        for ( $colNum = 1; $colNum <= $columns; $colNum++ )
        {
            $colLetter = ExcelReader::stringFromColumnIndex ( $colNum );
            $value = $this->getCellValue ( $colNum, $rowNum, true, true );
            $hash[] = $colLetter . ":" . $value;
        }
        $hash = implode ( "|", $hash );

        return $hash;
    }

    private function parseSheet ()
    {
        $maxRow = $this->sheet->getRowCount ();

        for ( $rowNum = 1; $rowNum <= $maxRow; $rowNum++ )
        {
            $this->currentRow = $rowNum;
            if ( $this->currentRow == $this->firstRow )
            {
                continue;
            }

            $item = $this->getRowData ( $rowNum, $this->header, true );
            $this->handleItem ( $item );
        }

        $this->success = true;
    }

    public function getRowData ( $rowNum, $header, $keep = true )
    {
        $item = new Product();
        $coreName = array_pop ( class_parents ( $item ) ) ? : get_class ( $item );
        $rc = new ReflectionClass ( $coreName );
        $props = $rc->getProperties ();
        foreach ( $props as $prop )
        {
            $propName = $prop->getName ();
            $trim = ($prop->getName () == 'top' ? false : true);
            $column = $prop->getValue ( $header );

            if ( stripos ( $column, "|" ) !== false )
            {
                $columns = explode ( '|', $column );
                $value = array ();
                foreach ( $columns as $column )
                {
                    $value[] = $this->getCellValue ( $column, $rowNum, $trim );
                }
            }
            else
            {
                $value = $this->getCellValue ( $column, $rowNum, $trim );
            }
            $prop->setValue ( $item, $value );
        }

        return $item;
    }

    private function getCellValue ( $col, $row, $trimmed = true, $noFormatted = false )
    {
        if ( is_null ( $col ) )
        {
            return null;
        }

        $col = is_numeric ( $col ) ? $col : ExcelReader::columnIndexFromString ( $col );
        $value = $noFormatted ? $this->sheet->getCellRawValue ( $col, $row ) : $this->sheet->getCellValue ( $col, $row );
        $retValue = $trimmed ? trim ( $value ) : $value;
        return $retValue;
    }

    private function handleItem ( $item )
    {

        if ( $item->top && !$item->price )
        {
            list($level, $categoryName) = explode ( '.', $item->top );
            if ( !is_numeric ( $level ) )
            {
                return;
            }
            $item->top = trim ( $categoryName );
            $this->priceList->setCurrentCategory ( $item->top, $level );
            return;
        }

        if ( $item->name && $item->price )
        {
            $item->price = floatval ( preg_replace ( '/[^0-9\.]+/', '', str_replace ( ',', '.', $item->price ) ) );
            $this->priceList->addItem ( $item );
        }
    }

    public function getPriceList ()
    {
        return $this->priceList;
    }

    public function attach ( \SplObserver $observer )
    {

    }

    public function detach ( \SplObserver $observer )
    {

    }

    public function notify ()
    {

    }
}
