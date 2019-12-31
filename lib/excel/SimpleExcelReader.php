<?php

require_once 'ExcelReader.php';
require_once 'ExcelReader/excel-reader.php';

/**
 * @file SimpleExcelReader
 * Парсер экселовских файлов на основе библиотеки Spreadsheet_Excel_Reader
 */
class SimpleExcelReader extends ExcelReader {

    private $reader;

    public function __construct ($filename, $encoding) {
        $this->reader = new Spreadsheet_Excel_Reader();
        $this->reader->setOutputEncoding ($encoding);
        $this->reader->read ($filename);
    }
    
    public function getRowGroupLevel ( $sheet, $row )
    {
        return null;
    }

    public function getCellStyle ($sheet, $column, $row, $styleNames) {
        return array();
    }

    public function getCellValue ($sheet, $column, $row) {
        $data = $this->reader->sheets[$sheet];

        if(!array_key_exists( $row, $data['cellsInfo'] ) OR !array_key_exists( $column, $data['cellsInfo'][$row] ))
            return null;

        $cell = $data['cellsInfo'][$row][$column];

        if(!array_key_exists( 'raw', $cell ))
            return null;

        $value = $cell['raw'];
        $format = array_key_exists('format', $cell) ? $cell['format'] : 'GENERAL';
        return PHPExcel_Style_NumberFormat::toFormattedString ($value, $format);
    }

    public function getColumnCount ($sheet) {
        return $this->reader->sheets[$sheet]['numCols'];
    }

    public function getRowCount ($sheet) {
        return $this->reader->sheets[$sheet]['numRows'];
    }

    public function getSheetCount () {
        return count ($this->reader->sheets);
    }

    public function getSheetTitle ($sheet) {
        return $this->reader->sheets[$sheet]['title'];
    }

    public function isEmpty($sheet)
    {
        return (!array_key_exists('cellsInfo', $this->reader->sheets[$sheet]) OR count($this->reader->sheets[$sheet]['cellsInfo']) == 0);
    }

    public function getCellRawValue($sheet, $column, $row)
    {
        $data = $this->reader->sheets[$sheet];

        if(!array_key_exists( $row, $data['cellsInfo'] ) OR !array_key_exists( $column, $data['cellsInfo'][$row] ))
            return null;

        $cell = $data['cellsInfo'][$row][$column];
        return array_key_exists('raw', $cell) ? $cell['raw'] : null;
    }

    public function unSetSheet($sheet)
    {
        $this->reader->sheets[$sheet] = 0;
    }
}

?>
