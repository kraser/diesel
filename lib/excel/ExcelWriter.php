<?php

require_once 'ExcelLib/PHPExcel.php';

class ExcelWriter
{
    public $filename;
    public $workbook;
    public $activeSheet;
    public $countryCode;
    public $tmpDir;
    public $version;

    function __construct( $filename = '-' )
    {
        $this->filename = $filename;
        $this->BIFFversion = 0x0600;
        $this->workbook = new PHPExcel();
    }

    function send( $filename )
    {
        //$this->filename = $filename;
        header( "Content-type: application/vnd.ms-excel" );
        header( "Content-Disposition: attachment; filename=\"$filename\"" );
        header( "Expires: 0" );
        header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
        header( "Pragma: public" );
    }

    public function addWorksheet( $title )
    {

        $activeSheet = new Work_Sheet( $this, $title ); //$objPHPExcel->getActiveSheet();
        //$activeSheet->setTitle(iconv("CP1251", "UTF-8", $title));
        $this->activeSheet = $activeSheet;

        return $activeSheet;
    }

    public function addFormat( $properties = array ( ) )
    {
        $format = new StyleFormat( $properties );
        $this->workbook->addCellXf( $format->style );

        return $format;
    }

    public function setCountry( $code )
    {
        $this->countryCode = $code;
    }

    function rowcolToCell( $row, $col )
    {
        $int = ( int ) ($col / 26);
        $frac = $col % 26;
        $chr1 = '';

        if ( $int > 0 )
            $chr1 = chr( ord( 'A' ) + $int - 1 );

        $chr2 = chr( ord( 'A' ) + $frac );
        $row++;

        return $chr1 . $chr2 . $row;
    }

    public function close()
    {
        $writer = PHPExcel_IOFactory::createWriter( $this->workbook, 'Excel5' );


        $writer->setTempDir( $this->tmpDir );
        $writer->save( $this->filename );
    }

    public function setTempDir( $dirname )
    {
        if ( is_dir( $dirname ) )
        {
            $this->tmpDir = $dirname;

            return true;
        }

        return false;
    }

    public function setVersion( $version )
    {
        $this->version = $version;
    }

    public function setCustomColor( $index, $red, $green, $blue )
    {
        //Обработать составляющие
        $color = new PHPExcel_Style_Color();
    }

    //В док на пир worksheets
    public function sheets()
    {
        return $this->workbook->getAllSheets();
    }
}

class Work_Sheet
{
    public $parent;
    public $sheet;
    public $encoding = "UTF-8";
    public $styles = array ( );

    public function __construct( $parent, $title )
    {
        $this->parent = $parent;
        $parent->workbook->setActiveSheetIndex( 0 );
        $this->sheet = $parent->workbook->getActiveSheet();
        $this->sheet->setTitle( $title ); //вынести iconv в вызов конструктора
    }

    public function getName()
    {
        return $this->sheet->getTitle();
    }

    function setInputEncoding( $encoding = "UTF-8" )
    {
        if ( $encoding != "UTF-8" )
            $this->encoding = $encoding;
    }

    public function select()
    {

    }

    public function activate()
    {

    }

    public function setFirstSheet()
    {

    }

    public function protect()
    {

    }

    function setColumn( $firstcol, $lastcol, $width, $format = null, $hidden = 0, $level = 0 )
    {
        for ( $i = $firstcol; $i <= $lastcol; $i++ )
        {
            $style = new stdClass();
            $style->width = $width;
            $style->format = $format;
            $this->styles[ $i ] = $style;
        }
    }

    public function writeCol( $row, $col, $values, $format = null )
    {
        if ( is_array( $values ) )
        {
            foreach ( $values as $value )
            {
                $this->write( $row, $col, $value, $format );
                $row++;
            }
        }
    }

    public function writeRow( $row, $col, $values, $format = null )
    {
        $retval = '';
        if ( is_array( $values ) )
        {
            foreach ( $values as $value )
            {
                if ( is_array( $value ) )
                    $this->writeCol( $row, $col, $value, $format );
                else
                    $this->write( $row, $col, $value, $format );

                $col++;
            }
        }
        else
            $retval = new PEAR_Error( '$val needs to be an array' );

        return($retval);
    }

    public function setSelection( $first_row, $first_column, $last_row, $last_column )
    {

    }

    public function freezePanes( $panes )
    {

    }

    public function thawPanes( $panes )
    {

    }

    public function hideScreenGridlines()
    {
        $this->sheet->setShowGridlines( false );
    }

    public function setPortrait()
    {
        $this->sheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT );
    }

    public function setLandscape()
    {
        $this->sheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
    }

    public function setPaper( $size = 1 )
    {
        $this->sheet->getPageSetup()->setPaperSize( $size );
    }

    public function setHeader( $string, $margin = 0.50 )
    {
        $header = $this->sheet->getHeaderFooter();
        $header->setOddHeader( $string );
        $header->setEvenHeader( $string );
        $this->sheet->getPageMargins()->setHeader( $margin );
    }

    public function setFooter( $string, $margin = 0.50 )
    {
        $header = $this->sheet->getHeaderFooter();
        $header->setOddFooter( $string );
        $header->setEvenFooter( $string );
        $this->sheet->getPageMargins()->setFooter( $margin );
    }

    public function setMerge( $firstRow, $firstCol, $lastRow, $lastCol )
    {
        $this->sheet->mergeCellsByColumnAndRow( $firstCol, $firstRow1, $lastCol, $lastRow );
    }

    public function centerHorizontally( $center )
    {
        $this->sheet->getPageSetup()->setHorizontalCentered( $center );
    }

    public function centerVertically( $center )
    {
        $this->sheet->getPageSetup()->setVerticalCentered( $center );
    }

    public function setMargins( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setLeft( $margin );
        $margin->setRight( $margin );
        $margin->setTop( $margin );
        $margin->setBottom( $margin );
    }

    public function setMargins_LR( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setLeft( $margin );
        $margin->setRight( $margin );
    }

    public function setMargins_TB( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setTop( $margin );
        $margin->setBottom( $margin );
    }

    public function setMarginLeft( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setLeft( $margin );
    }

    public function setMarginRight( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setRight( $margin );
    }

    public function setMarginTop( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setTop( $margin );
    }

    public function setMarginBottom( $margin )
    {
        $margins = $this->sheet->getPageMargins();
        $margin->setBottom( $margin );
    }

    public function repeatRows( $firstRow, $lastRow = null )
    {
        $this->sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( $firstRow, $lastRow );
    }

    public function printArea( $firstRow, $firstCol, $lastRow, $lastCol )
    {
        $this->sheet->getPageSetup()->setPrintAreaByColumnAndRow( $firstCol, $firstRow, $lastCol, $lastRow );
    }

    public function hideGridlines()
    {
        $this->sheet->setPrintGridlines( false );
    }

    public function printRowColHeaders( $print = 1 )
    {
        $this->sheet->setShowRowColHeaders( $print );
    }

    public function fitToPages( $width, $height )
    {
        $this->sheet->getPageSetup()->setFitToPage( true );
        $this->sheet->getPageSetup()->setFitToHeight( $height );
        $this->sheet->getPageSetup()->setFitToWidth( $width );
    }

    public function setHPagebreaks( $breaks )
    {

    }

    public function setVPagebreaks( $breaks )
    {

    }

    public function setZoom( $scale = 100 )
    {
        $this->sheet->getPageSetup()->setScale( $scale );
    }

    public function setPrintScale( $scale = 100 )
    {
        $this->sheet->getPageSetup()->setScale( $scale );
    }

    public function write( $row, $col, $token, $format = null )
    {
        /*
          $cellCoord = PHPExcel_Cell::stringFromColumnIndex ($col).($row+1);
          $this->sheet->setCellValueByColumnAndRow($col, $row + 1, $value);
          $format = $this->sheet->getStyle($cellCoord);
          $format->setFont($cellFormat->getFont());
          $format->setFont($cellFormat->getFont());
          $format->getAlignment()->setHorizontal($cellFormat->getAlignment()->getHorizontal());
          $format->getNumberFormat()->setFormatCode($cellFormat->getNumberFormat()->getFormatCode());
         */
        if ( preg_match( "/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $token ) )
            return $this->writeNumber( $row, $col, $token, $format ); // Match number
        elseif ( preg_match( "/^[fh]tt?p:\/\//", $token ) )
            return $this->writeUrl( $row, $col, $token, '', $format ); // Match http or ftp URL
        elseif ( preg_match( "/^mailto:/", $token ) )
            return $this->writeUrl( $row, $col, $token, '', $format ); // Match mailto:
        elseif ( preg_match( "/^(?:in|ex)ternal:/", $token ) )
            return $this->writeUrl( $row, $col, $token, '', $format ); // Match internal or external sheet link
        elseif ( preg_match( "/^=/", $token ) )
            return $this->writeFormula( $row, $col, $token, $format ); // Match formula
        elseif ( $token == '' )
            return $this->writeBlank( $row, $col, $format ); // Match blank
        else
            return $this->writeString( $row, $col, $token, $format ); // Default: match string
    }

    private function writeCell( $row, $col, $token, $format, $dataType = PHPExcel_Cell_DataType::TYPE_STRING )
    {
        if ( $this->encoding != "UTF-8" )
            $token = iconv( $this->encoding, "UTF-8", $token );

        $cellCoord = PHPExcel_Cell::stringFromColumnIndex( $col ) . ($row + 1);
        $cell = $this->sheet->getCellByColumnAndRow( $col, $row + 1 );
        $cell->setValueExplicit( $token, $dataType );
        if ( $format )
        {
            $xfIndex = $format->style->getIndex(); //echo $token."\n";
            $cell->setXfIndex( $xfIndex );
        }
    }

    public function writeNumber( $row, $col, $token, $format )
    {
        $this->writeCell( $row, $col, $token, $format, PHPExcel_Cell_DataType::TYPE_NUMERIC );
    }

    public function writeNote( $row, $col, $token, $format )
    {
        $this->writeCell( $row, $col, $token, $format, PHPExcel_Cell_DataType::TYPE_INLINE );
    }

    public function writeBlank( $row, $col, $format = null )
    {
        $cell = $this->sheet->getCellByColumnAndRow( $col, $row + 1 );
        if ( $format )
        {
            $xfIndex = $format->style->getIndex();
            $cell->setXfIndex( $xfindex );
        }
    }

    public function writeFormula( $row, $col, $token, $format = null )
    {
        $this->writeCell( $row, $col, $token, $format, PHPExcel_Cell_DataType::TYPE_FORMULA );
    }

    public function writeUrl( $row, $col, $token, $string = '', $format = null )
    {
        $this->writeCell( $row, $col, $token, $format, PHPExcel_Cell_DataType::TYPE_STRING );
    }

    public function writeString( $row, $col, $token, $format = null )
    {
        $this->writeCell( $row, $col, $token, $format, PHPExcel_Cell_DataType::TYPE_STRING );
    }

    public function setRow( $row, $height, $format = null, $hidden = false, $level = 0 )
    {
        $dim = $this->sheet->getRowDimension( $row );
        $dim->setRowHeight( $height );
        $dim->setXfIndex( $format->style->getIndex() );
        $dim->setVisible( !$hidden );
    }

    public function mergeCells( $firstRow, $firstCol, $lastRow, $lastCol )
    {
        $this->sheet->mergeCellsByColumnAndRow( $firstCol, $firstRow1, $lastCol, $lastRow );
    }

    public function insertBitmap( $row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1 )
    {

    }
}

class StyleFormat
{
    public $style;

    function __construct( $properties )
    {
        $this->style = new PHPExcel_Style();
        foreach ( $properties as $property => $value )
        {
            if ( method_exists( $this, 'set' . ucwords( $property ) ) )
            {
                $methodName = 'set' . ucwords( $property );
                $this->$methodName( $value );
            }
        }
    }

    function setAlign( $location )
    {
        $alignment = $this->style->getAlignment(); //->setHorizontal($alignment);
        switch ( $location )
        {
            case "left":
            case "right":
            case "center":
            case "justify":
                $alignment->setHorizontal( $location );
                break;
            case "centre":
                $alignment->setHorizontal( "center" );
                break;
            case "top":
            case "bottom":
                $alignment->setVertical( $location );
                break;
            case "vcenter":
            case "vcenter":
                $alignment->setVertical( "center" );
                break;
            case "vjustify":
                $alignment->setVertical( "justify" );
                break;
        }
    }

    function setVAlign( $alignment )
    {
        $this->style->getAlignment()->setVertical( $alignment );
    }

    function setHAlign( $alignment )
    {
        $this->style->getAlignment()->setHorizontal( $alignment );
    }

    function setMerge()
    {

    }

    function setLocked()
    {
        $this->style->getProtection()->setLocked( 'protected' );
    }

    function setUnLocked()
    {
        $this->style->getProtection()->setLocked( 'unprotected' );
    }

    function setBold( $weight = 1 )
    {
        $this->style->getFont()->setBold( $weight );
    }

    function setBottom( $thickness )
    {
        $thickness = $thickness == 1 ? PHPExcel_Style_Border::BORDER_THIN : PHPExcel_Style_Border::BORDER_THICK;
        $this->style->getBorders()->getBottom()->setBorderStyle( $thickness );
    }

    function setTop( $thickness )
    {
        $thickness = $thickness == 1 ? PHPExcel_Style_Border::BORDER_THIN : PHPExcel_Style_Border::BORDER_THICK;
        $this->style->getBorders()->getTop()->setBorderStyle( $thickness );
    }

    function setLeft( $thickness )
    {
        $thickness = $thickness == 1 ? PHPExcel_Style_Border::BORDER_THIN : PHPExcel_Style_Border::BORDER_THICK;
        $this->style->getBorders()->getLeft()->setBorderStyle( $thickness );
    }

    function setRight( $thickness )
    {
        $thickness = $thickness == 1 ? PHPExcel_Style_Border::BORDER_THIN : PHPExcel_Style_Border::BORDER_THICK;
        $this->style->getBorders()->getRight()->setBorderStyle( $thickness );
    }

    function setBorder( $thickness )
    {
        $thickness = $thickness == 1 ? PHPExcel_Style_Border::BORDER_THIN : PHPExcel_Style_Border::BORDER_THICK;
        $this->style->getBorders()->getBottom()->setBorderStyle( $thickness );
        $this->style->getBorders()->getTop()->setBorderStyle( $thickness );
        $this->style->getBorders()->getLeft()->setBorderStyle( $thickness );
        $this->style->getBorders()->getRight()->setBorderStyle( $thickness );
    }

    function setBorderColor( $color )
    {
        $this->setBottomColor( $color );
        $this->setTopColor( $color );
        $this->setLeftColor( $color );
        $this->setRightColor( $color );
    }

    function setBottomColor( $colorName )
    {
        $colorHex = $this->getColor( $colorName );
        $color = $this->style->getBorders()->getBottom()->getColor();
        $color->setRGB( $colorHex );
    }

    function setTopColor( $colorName )
    {
        $colorHex = $this->getColor( $colorName );
        $color = $this->style->getBorders()->getTop()->getColor();
        $color->setRGB( $colorHex );
    }

    function setLeftColor( $colorName )
    {
        $colorHex = $this->getColor( $colorName );
        $color = $this->style->getBorders()->getLeft()->getColor();
        $color->setRGB( $colorHex );
    }

    function setRightColor( $colorName )
    {
        $colorHex = $this->getColor( $colorName );
        $color = $this->style->getBorders()->getRight()->getColor();
        $color->setRGB( $colorHex );
    }

    function setFgColor( $color )
    {
        $this->style->getFont()->getColor()->setRGB( $this->getColor( $color ) );
    }

    function setBgColor( $color )
    {
        $this->style->getFill()->getStartColor()->setRGB( $this->getColor( $color ) );
    }

    function setColor( $color )
    {
        $this->style->getFill()->getStartColor()->setRGB( $this->getColor( $color ) );
    }

    function setPattern( $pattern )
    {
        //Реализовать
    }

    function setUnderline( $underline )
    {
        $this->style->getFont()->setUnderline( $underline );
    }

    function setItalic()
    {
        $this->style->getFont()->setItalic( true );
    }

    function setSize( $size )
    {
        $this->style->getFont()->setSize( $size );
    }

    function setTextWrap()
    {
        //Реализовать
    }

    function setTextRotation( $angle )
    {

    }

    function setNumFormat( $format )
    {
        $this->style->getNumberFormat()->setFormatCode( $format );
    }

    function setStrikeOut()
    {
        $this->style->getFont()->setStriketrough( true );
    }

    function setOutLine()
    {

    }

    function setShadow()
    {

    }

    function setScript( $script )
    {
        if ( $script == 1 )
            $this->style->getFont()->setSuperScript( true );
        else
            $this->style->getFont()->setSubScript( true );
    }

    function setFontFamily( $font )
    {
        $this->style->getFont()->setName( $font );
    }

    function getColor( $colorName = '' )
    {
        $colors = array (
            'aqua' => 0x07,
            'cyan' => 0x07,
            'black' => 0x00,
            'blue' => 0x04,
            'brown' => 0x10,
            'magenta' => 0x06,
            'fuchsia' => 0x06,
            'gray' => 0x17,
            'grey' => 0x17,
            'green' => 0x11,
            'lime' => 0x03,
            'navy' => 0x12,
            'orange' => 0x35,
            'purple' => 0x14,
            'red' => 0x02,
            'silver' => 0x16,
            'white' => 0x01,
            'yellow' => 0x05
        );

        // Return the default color, 0x7FFF, if undef,
        if ( $colorName === '' )
        {
            return(0x7FFF);
        }

        // or the color string converted to an integer,
        if ( isset( $colors[ $colorName ] ) )
        {
            return($colors[ $colorName ]);
        }

        // or the default color if string is unrecognised,
        if ( preg_match( "/\D/", $colorName ) )
        {
            return(0x7FFF);
        }

        // or the default color if arg is outside range,
        if ( $colorName > 63 )
        {
            return(0x7FFF);
        }

        // or an integer in the valid range
        return($colorName);
    }
}
?>
