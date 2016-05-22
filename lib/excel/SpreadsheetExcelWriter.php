<?php

require_once __DIR__ . "/Spreadsheet/Excel/Writer.php";

class SpreadsheetExcelWriter extends Spreadsheet_Excel_Writer {

    public function __construct($filename = '') {
        parent::__construct($filename);
    }
}
