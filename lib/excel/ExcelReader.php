<?php

/**
 * @file ExcelReader.php
 * Интерфейс для сокрытия реализации сторонних excel-парсеров
 */

abstract class ExcelReader {
    /** Количество листов в открытой книге */
    public abstract function getSheetCount();

    /** Результат проверки на пустоту листа
     *  @return Boolean
     */
    public abstract function isEmpty($sheet);
    
    /** Заголовок листа
     * @param int $sheet Номер листа (0 = первый лист)
     */
    public abstract function getSheetTitle( $sheet );

    /** Количество строк на листе
     * @param int $sheet Номер листа (0 = первый лист)
     */
    public abstract function getRowCount( $sheet );
    
    /** Количество колонок на листе
     * @param int $sheet Номер листа (0 = первый лист)
     */
    public abstract function getColumnCount( $sheet );
    
    /** Значение ячейки (форматированное)
     * @param int $sheet Номер листа (0 = первый лист)
     * @param int $column Номер колонуи (1 = первая колонка)
     * @param int $row Номер строки (1 = первая строка)
     */
    public abstract function getCellValue( $sheet, $column, $row );

    /** Значение ячейки (неформатированное)
     * @param int $sheet Номер листа (0 = первый лист)
     * @param int $column Номер колонуи (1 = первая колонка)
     * @param int $row Номер строки (1 = первая строка)
     */
    public abstract function getCellRawValue( $sheet, $column, $row );
    
    /** Уровень группировки строк
     * @param int $sheet Номер листа (0 = первый лист)
     * @param int $row Номер строки (1 = первая строка)
     */
    public abstract function getRowGroupLevel( $sheet, $row );
    
    /** Массив со стилями ячейки
     * @param int $sheet Номер листа (0 = первый лист)
     * @param int $column Номер колонуи (1 = первая колонка)
     * @param int $row Номер строки (1 = первая строка)
     * @param $styleNames - массив с названиями интересующих стилей
     *      Поддерживаются следующие стили: fontColor, fontSize, isBold, isItalic, indent, fillType, startColor
     */
    public abstract function getCellStyle( $sheet, $column, $row, $styleNames );
    
    /**
     * функция преобразует строковое обозначение колонки в integer
     * @param string $column Текстовый индекс колонки (A. B, ...)
     * A => 1, B => 2, ..
     */
    public static function columnIndexFromString ($column) {
        return PHPExcel_Cell::columnIndexFromString ($column);
    }

    /**
     * функция преобразует индекс (integer) колонки в строковое соответствие
     * @param int $column Числовой индекс колонки (1, 2, ...)
     * 1 => A, 2 => B, ..
     */
    public static function stringFromColumnIndex ($column) {
        return PHPExcel_Cell::stringFromColumnIndex ($column -1);
    }

}
?>
