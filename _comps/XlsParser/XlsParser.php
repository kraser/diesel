<?php
/**
 * <pre>Компонент XlsParser для чтения Xls-файлов</pre>
 *
 * @author kraser
 */
class XlsParser extends CmsComponent
{
    /**
     * @var Workbook <p>Собственно парсер</p>
     */
    private $engine;

    /**
     * @var Mixed <p>Объект-соответствие между свойствами объекта и колонками таблицы</p>
     */
    private $header;

    /**
     * @var WorkSheet <p>Текущий лист xls-Файла</p>
     */
    private $sheet;

    /**
     * @var String <p>Имя листа</p>
     */
    private $sheetName;

    /**
     * @var Integer <p>Номер текущей строки</p>
     */
    private $currentRow;

    /**
     * @var String <p>Имя класса объекта в котором сохраняются данные</p>
     */
    private $itemClass;

    /**
     * @var Callable <p>Обработчик объектов</p>
     */
    private $itemHandler;

    /**
     * @var Callable <p>Обработчик имен листов</p>
     */
    private $sheetNameHandler;

    /**
     * <pre>Конструктор</pre>
     * @param String $alias <p>Алиас компонента</p>
     * @param CmsComponent $parent <p>Владелец компонента</p>
     */
    public function __construct ( $alias, $parent )
    {
        parent::__construct (  $alias, $parent );
        Starter::import("libs.excel.WorkBook");
    }

    public function init ()
    {
        parent::init ();
    }

    /**
     * <pre>Устанавливает рабочее пространство парсера</pre>
     * @param String $itemClass <p>Имя класса объекта в который будет поомещен результат</p>
     * @param Mixed $header <p>Описатель соответствий [св-во обекта] -> [колонка таблицы]</p>
     * @param String $fileName <p>Имя xls-файла с данными</p>
     * @param Array $itemHandler <p></p>
     * @param Array $sheetNameHandler <p></p>
     */
    public function setWorkSpace ( $itemClass, $header, $fileName, $itemHandler = null, $sheetNameHandler = null )
    {
        $this->itemClass = $itemClass;
        $this->header = $header;
        $this->engine = new WorkBook ( $fileName );
        $this->itemHandler = $itemHandler;
        $this->sheetNameHandler = $sheetNameHandler;
    }

    /**
     * <pre>Запуск компонента на исполнение</pre>
     */
    public function run ()
    {
        $sheetCount = $this->engine->getSheetCount ();
        for ( $sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++ )
        {
            $sheet = $this->engine->getSheet ( $sheetIndex );
            $this->setSheet ( $sheet );

            if ( $this->isEmptySheet () )
                continue;

            $this->parseSheet ();
        }
    }

    /**
     * <pre>Устанавливает рабочий лист</pre>
     * @param WorkSheet $sheet <p>Лист</p>
     */
    private function setSheet ( $sheet )
    {
        $title = $sheet->getTitle ();
        $this->sheetName = $title;
        $this->sheet = $sheet;
    }

    /**
     * <pre>Возвращает флаг пустоты листа</pre>
     * @return Boolean
     */
    public function isEmptySheet ()
    {
        return $this->sheet->isEmpty ();
    }

    /**
     * <pre>Разбор (парсинг) листа</pre>
     * @return void
     */
    private function parseSheet ()
    {
        $maxRow = $this->sheet->getRowCount ();
        $result = $this->handleSheetName();
        if ( !$result)
            return;
        for ( $rowNum = 1; $rowNum <= $maxRow; $rowNum++ )
        {
            $this->currentRow = $rowNum;
            $item = $this->getRowData ( $rowNum, $this->header, true );
            $this->handleItem ( $item );
        }
    }

    /**
     * <pre>Считывание строки данных</pre>
     * @param Integer $rowNum <p>Номер разбираемой строки</p>
     * @param Mixed $header <p>Объект-соответствие</p>
     * @return Mixed
     */
    public function getRowData ( $rowNum, $header )
    {
        $rc = new ReflectionClass ( $this->itemClass );
        $item = $rc->newInstance ();
        $props = $rc->getProperties ();
        foreach ( $props as $prop )
        {
            $column = $prop->getValue ( $header );
            if ( !$column )
                continue;

            $value = $this->getCellValue ( $column, $rowNum );
            $prop->setValue ( $item, $value );
        }

        return $item;
    }

    /**
     * <pre>Чтение определенной ячейки листа</pre>
     * @param String $col <p>Колонка</p>
     * @param Integer $row <p>Номер строки</p>
     * @param Boolean $trimmed <p>Флаг обрезания краевых пробелов</p>
     * @param Boolean $noFormatted <p>Флан форматирования значения</p>
     * @return String
     */
    private function getCellValue ( $col, $row, $trimmed = true, $noFormatted = false )
    {
        if ( is_null ( $col ) )
            return null;

        $col = is_numeric ( $col ) ? $col : ExcelReader::columnIndexFromString ( $col );
        $value = $noFormatted ? $this->sheet->getCellRawValue ( $col, $row ) : $this->sheet->getCellValue ( $col, $row );
        $retValue = $trimmed ? trim ( $value ) : $value;
        return $retValue;
    }

    /**
     * <pre>Вызов обработчика объекта</pre>
     * @param Mixed $item
     */
    private function handleItem ( $item )
    {
        if ( $this->itemHandler )
        {
            $object = $this->itemHandler[0];
            $methodName = $this->itemHandler[1];
            $method = new ReflectionMethod ( $object, $methodName );
            $method->invoke ( $object, $item );
        }
    }

    /**
     * <pre>Вызов обработчика имени листа</pre>
     * @return Boolean
     */
    private function handleSheetName ()
    {
        if ( $this->sheetNameHandler )
        {
            $object = $this->sheetNameHandler[0];
            $methodName = $this->sheetNameHandler[1];
            $method = new ReflectionMethod ( $object, $methodName );
            return $method->invoke ( $object, $this->sheetName );
        }
        else
            return true;
    }
}
