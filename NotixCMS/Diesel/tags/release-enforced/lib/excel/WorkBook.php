<?php

require_once 'SimpleExcelReader.php';
require_once 'AdvancedExcelReader.php';

class WorkBook {

    const NO_READER = 'DISABLED';
    const SIMPLE_READER = 'SimpleExcelReader';
    const ADVANCED_READER = 'AdvancedExcelReader';

    public $reader;
    private $filename;
    private $encoding;

    public function __construct ($filename, $readerType = null, $encoding = 'UTF-8') {
        $this->reader = self::createReader ($filename, $readerType, $encoding);
        $this->filename = $filename;
        $this->encoding = $encoding;
    }

    public function getSheetCount () {
        return $this->reader->getSheetCount ();
    }

    public function getSheet ($index) {
        return new WorkSheet ($this, $index);
    }

    public function getReader () {
        return $this->reader;
    }

    public function setReaderType ($readerType) {
        if ($this->reader && get_class ($this->reader) != $readerType) {
            $this->reader = new $readerType ($this->filename, $this->encoding);
        }
    }

    private static function createReader ($filename, $readerType, $encoding) {
        $readerType = $readerType == self::ADVANCED_READER ? self::ADVANCED_READER : self::guessReaderTypeFromFileContent ($filename);
        if ($readerType) {
            return new $readerType ($filename, $encoding);
        } else {
            throw Exception ("*******");
        }
    }

    /** Делает предположение о типе файла на основе его имени и первых двух байтов
     */
    private static function guessReaderTypeFromFileContent ($filename) {
        $headTwoBytes = file_get_contents ($filename, NULL, NULL, 0, 2);
        $fileType = dechex (ord ($headTwoBytes[0])) . dechex (ord ($headTwoBytes[1]));
        $pathinfo = pathinfo ($filename);
        if ((isset ($pathinfo['extension']) && 'xls' == strtolower ($pathinfo['extension'])) AND strtoupper ($fileType) == 'D0CF') {
            return self::SIMPLE_READER;
        } else {
            return self::ADVANCED_READER;
        }
    }

    public function free () {

    }

    public function getParserName () {
        return get_class ($this->reader);
    }

}

class WorkSheet {

    private $parent;
    private $index;

    public function __construct ($parent, $index) {
        $this->parent = $parent;
        $this->index = $parent->index = $index;
    }

    public function getTitle () {
        return $this->parent->reader->getSheetTitle ($this->index);
    }

    public function setReaderType ($readerType) {
        $this->parent->setReaderType ($readerType);
    }

    public function getRowCount () {
        return $this->parent->reader->getRowCount ($this->index);
    }

    /** Количество колонок на листе */
    public function getColumnCount () {
        return $this->parent->reader->getColumnCount ($this->index);
    }

    /** Значение ячейки (форматированное) */
    public function getCellValue ($column, $row) {
        return $this->parent->reader->getCellValue ($this->index, $column, $row);
    }

    /** Значение ячейки (неформатированное) */
    public function getCellRawValue($column, $row)
    {
        return $this->parent->reader->getCellRawValue ($this->index, $column, $row);
    }

    /** Проверка листа на пустоту */
    public function isEmpty()
    {
        return $this->parent->reader->isEmpty ($this->index);
    }

    public function getCellStyle ($column, $row, $styleNames) {
        $this->parent->setReaderType (WorkBook::ADVANCED_READER);
        return $this->parent->reader->getCellStyle ($this->index, $column, $row, $styleNames);
    }
    
    /** Получение значения уровня группировки строки с номером $rowNum */
    public function getRowGroupLevel ($rowNum) {
        $this->parent->setReaderType (WorkBook::ADVANCED_READER);
        return $this->parent->reader->getRowGroupLevel($this->index, $rowNum);
    }

    public function unSetSheet()
    {
        $this->parent->reader->unSetSheet ($this->index);
    }

}

class WorkSheet_ {

    private $typeReader = false;
    private $sheet = null;
    private $_parent = null;

    public function __construct ($sheet, $typeReader, $parent) {
        $this->sheet = $sheet;
        $this->typeReader = $typeReader;
        $this->_parent = $parent;
    }

    public function getHighestRow () {
        if ($this->typeReader)
            return $this->sheet->getHighestRow ();
        else
            return $this->sheet['numRows'] ? $this->sheet['numRows'] : 1;
    }

    public function getHighestColumn () {
        if ($this->typeReader)
            $result = $this->sheet->getHighestColumn ();
        else {
            $result = $this->sheet['numCols'];
//            $result = Excel::stringFromColumnIndex ($result);
            $result = Excel::stringFromColumnIndex ($result ? $result - 1 : $result);
        }
        return $result;
    }

    public function getCellValue ($pColumn = 0, $pRow = 1) {
        if ($this->typeReader) {
            $cellValue = $this->sheet->getCellByColumnAndRow ($pColumn, $pRow)->getCalculatedValue ();
            if ($cellValue)
                $cellValue = iconv ('UTF-8', 'CP1251', $cellValue);
            return $cellValue;
        }
        else
            return $this->sheet['cells'][$pRow][$pColumn + 1];
    }

    public function getFormattedValue ($pColumn = 0, $pRow = 1) {
        if ($this->typeReader) {
            $cellValue = $this->sheet->getCellByColumnAndRow ($pColumn, $pRow)->getFormattedValue ();
            if ($cellValue)
                $cellValue = iconv ('UTF-8', 'CP1251', $cellValue);
            return $cellValue;
        }
        else {
            $result = $this->sheet['cellsInfo'][$pRow][$pColumn + 1];
            $value = $result['raw'];
            $format = $result['format'];
            return PHPExcel_Style_NumberFormat::toFormattedString ($value, $format);
        }
    }

    public function getSheetTitle () {
        if ($this->typeReader)
            return iconv ('UTF-8', 'CP1251', $this->sheet->getTitle ());
        else
            return $this->sheet['title'];
    }

    public function setSheet ($workSheet) {
        if ($this->sheet) {
            $this->sheet = $workSheet;
            $this->typeReader = Excel::getTypeReader ();
        }
    }

    /**
     * Читает стили заданной ячейки и возвращает затребованные параметры стиля
     * @param array $styleParams список параметров стиля для получения
     * $styleParams = array('fontColor', 'fontSize', 'isBold', 'isItalic', 'indent', 'fillType', 'startColor', 'endColor');
     * @param string $column адрес колонки с ячейкой
     * @param string $row адрес строки с ячейкой
     * @return array
     */
    public function getStyle ($styleParams, $column, $row) {
        if (!is_array ($styleParams))
            $styleParams = array($styleParams);
        $returnedStyle = array();
        $_column = Excel::stringFromColumnIndex ($column);
        if ($this->_parent->extendParser ()) {
            $style = $this->_parent->getReader ()->getActiveSheet ()->getStyle ($_column . $row);
            foreach ($styleParams as $styleParameter) {
                switch ($styleParameter) {
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
                $returnedStyle[$styleParameter] = $value;
            }
        }
        return $returnedStyle;
    }

}

?>
