<?php
require_once 'ExcelReader.php';
require_once 'ExcelLib/PHPExcel.php';

/**
 * @file AdvancedExcelReader
 * Парсер экселовских файлов на основе продвинутой библиотеки ExcelLib
 */
class AdvancedExcelReader extends ExcelReader {

    private $reader;
    private $encoding;

    public function __construct ($filename, $encoding) {
        $this->reader = PHPExcel_IOFactory::load ($filename);
        $this->encoding = $encoding;
    }

    public function getRowGroupLevel($sheet, $rowNum)
    {
        $workSheet = $this->reader->getSheet($sheet);
        $dim = $workSheet->getRowDimension($rowNum);
        $level = $dim->getOutlineLevel();
        
        return $level;
    }
    
    public function getCellStyle ($sheet, $column, $row, $styleNames) {
        $coordinate = self::getCellCoordinate( $column, $row);

        $style = $this->reader->getSheet ($sheet)->getStyle ($coordinate);
        $resultStyle = array ();
        foreach ($styleNames as $styleName) {
            switch ($styleName) {
                case 'fontColor':
                    $value = $style->getFont ()->getColor ()->getRGB ();
                    break;
                case 'fontSize':
                    $value = $style->getFont ()->getSize ();
                    break;
                case 'isBold':
                    $value = $style->getFont ()->getBold ();
                    break;
                case 'isItalic':
                    $value = $style->getFont ()->getItalic ();
                    break;
                case 'indent':
                    $value = $style->getAlignment ()->getIndent ();
                    break;
                case 'fillType':
                    $value = $style->getFill ()->getFillType ();
                    break;
                case 'startColor':
                    $value = $style->getFill ()->getStartColor ()->getRGB ();
                    break;
                case 'endColor':
                    $value = $style->getFill ()->getEndColor ()->getRGB ();
                    break;
                default :
                    $value = null;
            }
            $resultStyle[$styleName] = $value;
        }
        return $resultStyle;
    }

    public function getCellValue ($sheet, $column, $row) {
        $coordinate = self::getCellCoordinate( $column, $row);

        $value = self::iconv($this->reader->getSheet ($sheet)->getCell ($coordinate)->getFormattedValue ());

        return $value;
    }

    public function getCellRawValue($sheet, $column, $row)
    {
        $coordinate = self::getCellCoordinate( $column, $row);
        $cellValue = $this->reader->getSheet ($sheet)->getCell ($coordinate)->getCalculatedValue ();
        return $cellValue;
    }

    public function getColumnCount ($sheet) {
        return self::columnIndexFromString ($this->reader->getSheet ($sheet)->getHighestColumn ());
    }

    public function getRowCount ($sheet) {
        return $this->reader->getSheet ($sheet)->getHighestRow ();
    }

    public function getSheetCount () {
        return $this->reader->getSheetCount ();
    }

    public function getSheetTitle ($sheet) {
        return self::iconv ($this->reader->getSheet ($sheet)->getTitle ());
    }

    private function iconv ($text) {
        if ($this->encoding == 'UTF-8') {
            return $text;
        } else {
            if(strlen($text)!=0){
                if(substr($text,-1)=="\xd0"){
                    $text = substr($text,0,strlen($text)-1);
                }
            }
            return iconv ('UTF-8', $this->encoding."//TRANSLIT", $text);
        }
    }

    public function isEmpty($sheet)
    {
        return !(sizeof($this->reader->getSheet($sheet)->getCellCollection(false)) > 0);
    }

    private static function getCellCoordinate( $column, $row ) {
        $columnLetter = self::stringFromColumnIndex ($column);
        return $columnLetter . $row;
    }

    public function unSetSheet($sheet)
    {
        $this->reader->unSetSheet($sheet);
    }
}

?>
