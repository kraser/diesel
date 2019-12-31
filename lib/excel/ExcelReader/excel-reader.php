<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A class for reading Microsoft Excel Spreadsheets.
 *
 * Originally developed by Vadim Tkachenko under the name PHPExcelReader.
 * (http://sourceforge.net/projects/phpexcelreader)
 * Based on the Java version by Andy Khan (http://www.andykhan.com).  Now
 * maintained by David Sanders.  Reads only Biff 7 and Biff 8 formats.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Spreadsheet
 * @package    Spreadsheet_Excel_Reader
 * @author     Vadim Tkachenko <vt@apachephp.com>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: reader.php 19 2007-03-13 12:42:41Z shangxiao $
 * @link       http://pear.php.net/package/Spreadsheet_Excel_Reader
 * @see        OLE, Spreadsheet_Excel_Writer
 */

require_once __DIR__ . '/OLERead.php';
define( 'PARSESHEET_WRONG_BIFF_VERSION', -1 );
define( 'PARSESHEET_NOT_WORKSHEET', -2 );
define( 'PARSESHEET_SUCCESS', 1 );

define( 'SPREADSHEET_EXCEL_READER_BIFF8', 0x600 );
define( 'SPREADSHEET_EXCEL_READER_BIFF7', 0x500 );
define( 'SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS', 0x5 );
define( 'SPREADSHEET_EXCEL_READER_WORKSHEET', 0x10 );

define( 'SPREADSHEET_EXCEL_READER_TYPE_BOF', 0x809 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_EOF', 0x0a );
define( 'SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET', 0x85 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_DIMENSION', 0x200 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_ROW', 0x208 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_DBCELL', 0xd7 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FILEPASS', 0x2f );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NOTE', 0x1c );
define( 'SPREADSHEET_EXCEL_READER_TYPE_TXO', 0x1b6 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_RK', 0x7e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_RK2', 0x27e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MULRK', 0xbd );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MULBLANK', 0xbe );
define( 'SPREADSHEET_EXCEL_READER_TYPE_INDEX', 0x20b );
define( 'SPREADSHEET_EXCEL_READER_TYPE_SST', 0xfc );
define( 'SPREADSHEET_EXCEL_READER_TYPE_EXTSST', 0xff );
define( 'SPREADSHEET_EXCEL_READER_TYPE_CONTINUE', 0x3c );
define( 'SPREADSHEET_EXCEL_READER_TYPE_LABEL', 0x204 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_LABELSST', 0xfd );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NUMBER', 0x203 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NAME', 0x18 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_ARRAY', 0x221 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_STRING', 0x207 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMULA', 0x406 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMULA2', 0x6 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMAT', 0x41e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_XF', 0xe0 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_BOOLERR', 0x205 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN', 0xffff );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS', 0xE5 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_CODEPAGE', 0x42 );

define( 'SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS', 25569 );
define( 'SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107 );
define( 'SPREADSHEET_EXCEL_READER_MSINADAY', 86400 );
//define('SPREADSHEET_EXCEL_READER_MSINADAY', 24 * 60 * 60);
//define('SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT', "%.2f");
define( 'SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT', "%s" );


/*
 * Place includes, constant defines and $_GLOBAL settings here.
 * Make sure they have appropriate docblocks to avoid phpDocumentor
 * construing they are documented by the page-level docblock.
 */

/**
 * A class for reading Microsoft Excel Spreadsheets.
 *
 * Originally developed by Vadim Tkachenko under the name PHPExcelReader.
 * (http://sourceforge.net/projects/phpexcelreader)
 * Based on the Java version by Andy Khan (http://www.andykhan.com).  Now
 * maintained by David Sanders.  Reads only Biff 7 and Biff 8 formats.
 *
 * @category   Spreadsheet
 * @package    Spreadsheet_Excel_Reader
 * @author     Vadim Tkachenko <vt@phpapache.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @see        OLE, Spreadsheet_Excel_Writer
 */
class Spreadsheet_Excel_Reader
{

    /**
     * Array of worksheets found
     *
     * @var array
     * @access public
     */
    var $boundsheets = array( );
    /**
     * Array of format records found
     *
     * @var array
     * @access public
     */
    var $formatRecords = array(
        0x00 => "General",
        0x01 => "0",
        0x02 => "0.00",
        0x03 => "#,##0",
        0x04 => "#,##0.00",
        0x09 => "0%",
        0x0e => "DD.MM.YYYY",
        0x15 => "h:mm:ss",
        0x16 => "m/d/yy h:mm"
    );
    /* ���������� ������� Excel (�� ���������) */
    /*
      0x00 => "General",
      0x01 => "0",
      0x02 => "0.00",
      0x03 => "#,##0",
      0x04 => "#,##0.00",
      0x05 => "($#,##0_);($#,##0)",
      0x06 => "($#,##0_);[Red]($#,##0)",
      0x07 => "($#,##0.00_);($#,##0.00)",
      0x08 => "($#,##0.00_);[Red]($#,##0.00)",
      0x09 => "0%",
      0x0a => "0.00%",
      0x0b => "0.00E+00",
      0x0c => "# ?/?",
      0x0d => "# ??/??",
      0x0e => "m/d/yy",
      0x0f => "d-mmm-yy",
      0x10 => "d-mmm",
      0x11 => "mmm-yy",
      0x12 => "h:mm AM/PM",
      0x13 => "h:mm:ss AM/PM"
      0x14 => "h:mm",
      0x15 => "h:mm:ss",
      0x16 => "m/d/yy h:mm",
      0x25 => "(#,##0_);(#,##0)",
      0x26 => "(#,##0_);[Red](#,##0)",
      0x27 => "(#,##0.00_);(#,##0.00)",
      0x28 => "(#,##0.00_);[Red](#,##0.00)",
      0x29 => "_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)",
      0x2a => "_($* #,##0_);_($* (#,##0);_($* "-"_);_(@_)",
      0x2b => "_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)",
      0x2c => "_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)",
      0x2d => "mm:ss",
      0x2e => "[h]:mm:ss",
      0x2f => "mm:ss.0",
      0x30 => "##0.0E+0",
      0x31 => "@"
     */

    /**
     * todo
     *
     * @var array
     * @access public
     */
    var $sst = array( );

    /**
     * Array of worksheets
     *
     * The data is stored in 'cells' and the meta-data is stored in an array
     * called 'cellsInfo'
     *
     * Example:
     *
     * $sheets  -->  'cells'  -->  row --> column --> Interpreted value
     *          -->  'cellsInfo' --> row --> column --> 'type' - Can be 'date', 'number', or 'unknown'
     *                                            --> 'raw' - The raw data that Excel stores for that data cell
     *
     * @var array
     * @access public
     */
    var $sheets = array( );

    /**
     * The data returned by OLE
     *
     * @var string
     * @access public
     */
    var $data;

    /**
     * OLE object for reading the file
     *
     * @var OLE object
     * @access private
     */
    var $_ole;

    /**
     * Default encoding
     *
     * @var string
     * @access private
     */
    var $_defaultEncoding = 'UTF-8';

    /**
     * Default number format
     *
     * @var integer
     * @access private
     */
    var $_defaultFormat = SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT;

    /**
     * todo
     * List of formats to use for each column
     *
     * @var array
     * @access private
     */
    var $_columnsFormat = array( );

    /**
     * todo
     *
     * @var integer
     * @access private
     */
    var $_rowoffset = 1;

    /**
     * todo
     *
     * @var integer
     * @access private
     */
    var $_coloffset = 1;

    /**
     * List of default date formats used by Excel
     *
     * @var array
     * @access public
     */
    var $dateFormats = array(
        0xe => "d/m/Y",
        0xf => "d-M-Y",
        0x10 => "d-M",
        0x11 => "M-Y",
        0x12 => "h:i a",
        0x13 => "h:i:s a",
        0x14 => "H:i",
        0x15 => "H:i:s",
        0x16 => "d/m/Y H:i",
        0x2d => "i:s",
        0x2e => "H:i:s",
        0x2f => "i:s.S"
    );

    /**
     * Default number formats used by Excel
     *
     * @var array
     * @access public
     */
    var $numberFormats = array(
        0x1 => "%1.0f", // "0"
        0x2 => "%1.2f", // "0.00",
        0x3 => "%1.0f", //"#,##0",
        0x4 => "%1.2f", //"#,##0.00",
        0x5 => "%1.0f", /* "$#,##0;($#,##0)", */
        0x6 => '$%1.0f', /* "$#,##0;($#,##0)", */
        0x7 => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x8 => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x9 => '%1.0f%%', // "0%"
        0xa => '%1.2f%%', // "0.00%"
        0xb => '%1.2f', // 0.00E00",
        0x25 => '%1.0f', // "#,##0;(#,##0)",
        0x26 => '%1.0f', //"#,##0;(#,##0)",
        0x27 => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x28 => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x29 => '%1.0f', //"#,##0;(#,##0)",
        0x2a => '$%1.0f', //"$#,##0;($#,##0)",
        0x2b => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x2c => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x30 => '%1.0f'    //"##0.0E0";
    );

    /**
     * ������� ��� �������
     *
     * @var String
     * @access public
     */
    var $formatCode;

    /**
     * ������� ������ XF �������
     *
     * @var Integer
     */
    var $xfindex;

    /**
     * ��������� ������������ ��� ��������� ������
     *
     * @var String
     */
    var $codePage = 'CP1251';

    /**
     * Constructor
     *
     * Some basic initialisation
     */
    function Spreadsheet_Excel_Reader()
    {
        $this->_ole = new OLERead();
    }

    /**
     * Set the encoding method
     *
     * @param string Encoding to use
     * @access public
     */
    function setOutputEncoding( $encoding )
    {
        $this->_defaultEncoding = $encoding;
    }

    /**
     * todo
     *
     * @access public
     * @param offset
     */
    function setRowColOffset( $iOffset )
    {
        $this->_rowoffset = $iOffset;
        $this->_coloffset = $iOffset;
    }

    /**
     * Set the default number format
     *
     * @access public
     * @param Default format
     */
    function setDefaultFormat( $sFormat )
    {
        $this->_defaultFormat = $sFormat;
    }

    /**
     * Force a column to use a certain format
     *
     * @access public
     * @param integer Column number
     * @param string Format
     */
    function setColumnFormat( $column, $sFormat )
    {
        $this->_columnsFormat[$column] = $sFormat;
    }

    /**
     * Read the spreadsheet file using OLE, then parse
     *
     * @access public
     * @param filename
     * @todo return a valid value
     */
    function read( $sFileName )
    {
        $res = $this->_ole->read( $sFileName );

        // oops, something goes wrong (Darko Miljanovic)
        if ( $res === false )
        {
            // check error code
            if ( $this->_ole->error == 1 )
            {
                // bad file
                die( 'The filename ' . $sFileName . ' is not readable' );
            }
            // check other error codes here (eg bad fileformat, etc...)
        }

        $this->data = $this->_ole->getWorkBook();
        $this->_parse();
    }

    /**
     * Parse a workbook
     *
     * @access private
     * @return bool
     */
    function _parse()
    {
        $pos = 0;

        $code = ord( $this->data[$pos] ) | ord( $this->data[$pos + 1] ) << 8;
        $length = ord( $this->data[$pos + 2] ) | ord( $this->data[$pos + 3] ) << 8;

        $version = ord( $this->data[$pos + 4] ) | ord( $this->data[$pos + 5] ) << 8;
        $substreamType = ord( $this->data[$pos + 6] ) | ord( $this->data[$pos + 7] ) << 8;

        if ( ($version != SPREADSHEET_EXCEL_READER_BIFF8) &&
            ($version != SPREADSHEET_EXCEL_READER_BIFF7) )
        {
            return false;
        }

        if ( $substreamType != SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS )
        {
            return false;
        }

        $pos += $length + 4;

        $code = ord( $this->data[$pos] ) | ord( $this->data[$pos + 1] ) << 8;
        $length = ord( $this->data[$pos + 2] ) | ord( $this->data[$pos + 3] ) << 8;

        while ( $code != SPREADSHEET_EXCEL_READER_TYPE_EOF )
        {
            switch ( $code )
            {
                case SPREADSHEET_EXCEL_READER_TYPE_SST:
                    $spos = $pos + 4;
                    $limitpos = $spos + $length;
                    $uniqueStrings = $this->_GetInt4d( $this->data, $spos + 4 );
                    $spos += 8;
                    for ( $i = 0; $i < $uniqueStrings; $i++ )
                    {
                        // Read in the number of characters
                        if ( $spos == $limitpos )
                        {
                            $opcode = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                            $conlength = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                            if ( $opcode != 0x3c )
                            {
                                return -1;
                            }
                            $spos += 4;
                            $limitpos = $spos + $conlength;
                        }
                        $numChars = ord( $this->data[$spos] ) | (ord( $this->data[$spos + 1] ) << 8);
                        $spos += 2;
                        $optionFlags = ord( $this->data[$spos] );
                        $spos++;
                        $asciiEncoding = (($optionFlags & 0x01) == 0);
                        $extendedString = ( ($optionFlags & 0x04) != 0);

                        // See if string contains formatting information
                        $richString = ( ($optionFlags & 0x08) != 0);

                        if ( $richString )
                        {
                            // Read in the crun
                            $formattingRuns = ord( $this->data[$spos] ) | (ord( $this->data[$spos + 1] ) << 8);
                            $spos += 2;
                        }

                        if ( $extendedString )
                        {
                            // Read in cchExtRst
                            $extendedRunLength = $this->_GetInt4d( $this->data, $spos );
                            $spos += 4;
                        }

                        $len = ($asciiEncoding) ? $numChars : $numChars * 2;
                        if ( $spos + $len < $limitpos )
                        {
                            $retstr = substr( $this->data, $spos, $len );
                            $spos += $len;
                        }
                        else
                        {
                            // found countinue
                            $retstr = substr( $this->data, $spos, $limitpos - $spos );
                            $bytesRead = $limitpos - $spos;
                            $charsLeft = $numChars - (($asciiEncoding) ? $bytesRead : ($bytesRead / 2));
                            $spos = $limitpos;

                            while ( $charsLeft > 0 )
                            {
                                $opcode = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                                $conlength = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                                if ( $opcode != 0x3c )
                                {
                                    return -1;
                                }
                                $spos += 4;
                                $limitpos = $spos + $conlength;
                                $option = ord( $this->data[$spos] );
                                $spos += 1;
                                if ( $asciiEncoding && ($option == 0) )
                                {
                                    $len = min( $charsLeft, $limitpos - $spos ); // min($charsLeft, $conlength);
                                    $retstr .= substr( $this->data, $spos, $len );
                                    $charsLeft -= $len;
                                    $asciiEncoding = true;
                                }
                                elseif ( !$asciiEncoding && ($option != 0) )
                                {
                                    $len = min( $charsLeft * 2, $limitpos - $spos ); // min($charsLeft, $conlength);
                                    $retstr .= substr( $this->data, $spos, $len );
                                    $charsLeft -= $len / 2;
                                    $asciiEncoding = false;
                                }
                                elseif ( !$asciiEncoding && ($option == 0) )
                                {
                                    // Bummer - the string starts off as Unicode, but after the
                                    // continuation it is in straightforward ASCII encoding
                                    $len = min( $charsLeft, $limitpos - $spos ); // min($charsLeft, $conlength);
                                    for ( $j = 0; $j < $len; $j++ )
                                    {
                                        $retstr .= $this->data[$spos + $j] . chr( 0 );
                                    }
                                    $charsLeft -= $len;
                                    $asciiEncoding = false;
                                }
                                else
                                {
                                    $newstr = '';
                                    for ( $j = 0; $j < strlen( $retstr ); $j++ )
                                    {
                                        $newstr = $retstr[$j] . chr( 0 );
                                    }
                                    $retstr = $newstr;
                                    $len = min( $charsLeft * 2, $limitpos - $spos ); // min($charsLeft, $conlength);
                                    $retstr .= substr( $this->data, $spos, $len );
                                    $charsLeft -= $len / 2;
                                    $asciiEncoding = false;
                                    //echo "Izavrat\n";
                                }
                                $spos += $len;
                            }
                        }
                        $retstr = PHPExcel_Reader_Excel5::_encodeUTF16($retstr, $asciiEncoding);
                        
                        if ( $richString )
                        {
                            $spos += 4 * $formattingRuns;
                        }

                        // For extended strings, skip over the extended string data
                        if ( $extendedString )
                        {
                            $spos += $extendedRunLength;
                        }
                        $this->sst[] = $retstr;
                    }
                    break;

                case SPREADSHEET_EXCEL_READER_TYPE_FILEPASS:
                    return false;
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_NAME:
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_FORMAT:
                    $indexCode = ord( $this->data[$pos + 4] ) | ord( $this->data[$pos + 5] ) << 8;

                    if ( $version == SPREADSHEET_EXCEL_READER_BIFF8 )
                    {
                        $numchars = ord( $this->data[$pos + 6] ) | ord( $this->data[$pos + 7] ) << 8;
                        if ( ord( $this->data[$pos + 8] ) == 0 )
                        {
                            $formatString = substr( $this->data, $pos + 9, $numchars );
                            $formatString = iconv('ASCII', "$this->_defaultEncoding//TRANSLIT", $formatString);
                        }
                        else
                        {
                            $formatString = substr( $this->data, $pos + 9, $numchars * 2 );
                            $formatString = iconv('UTF-16LE', "$this->_defaultEncoding//TRANSLIT", $formatString);
                        }
                    }
                    else
                    {
                        $numchars = ord( $this->data[$pos + 6] );
                        if( strstr( $this->codePage, "UTF" ) ) {
                            $formatString = substr( $this->data, $pos + 7, $numchars * 2 );
                            
                        } else {
                            $formatString = substr( $this->data, $pos + 7, $numchars );
                        }
                        $formatString = iconv($this->codePage, "$this->_defaultEncoding//TRANSLIT", $formatString);
                    }
                    $this->formatRecords[$indexCode] = $formatString;
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_XF:
                    $indexCode = ord( $this->data[$pos + 6] ) | ord( $this->data[$pos + 7] ) << 8;
                    $formatCode = isset( $this->formatRecords[$indexCode] ) ? $this->formatRecords[$indexCode] : '';

                    if ( array_key_exists( $indexCode, $this->dateFormats ) )
                    {
                        $this->formatRecords['xfrecords'][] = array
                            (
                            'type' => 'date',
                            'format' => $this->dateFormats[$indexCode],
                            'code' => $indexCode,
                            'formatCode' => $formatCode
                        );
                    }
                    elseif ( array_key_exists( $indexCode, $this->numberFormats ) )
                    {
                        $this->formatRecords['xfrecords'][] = array
                            (
                            'type' => 'number',
                            'format' => $this->numberFormats[$indexCode],
                            'code' => $indexCode,
                            'formatCode' => $formatCode
                        );
                    }
                    else
                    {
                        $isdate = FALSE;
                        if ( $indexCode > 0 )
                        {
                            $formatstr = null;
                            if ( isset( $this->formatRecords[$indexCode] ) )
                            {
                                $formatstr = $this->formatRecords[$indexCode];
                            }
                            if ( $formatstr )
                            {
                                if ( preg_match( "/[^hmsday\/\-:\s]/i", $formatstr ) == 0 )
                                { // found day and time format
                                    $isdate = TRUE;
                                    $formatstr = str_replace( 'mm', 'i', $formatstr );
                                    $formatstr = str_replace( 'h', 'H', $formatstr );
                                    //echo "\ndate-time $formatstr \n";
                                }
                            }
                        }

                        if ( $isdate )
                        {
                            $this->formatRecords['xfrecords'][] = array
                                (
                                'type' => 'date',
                                'format' => $formatstr,
                                'code' => $indexCode,
                                'formatCode' => $formatCode
                            );
                        }
                        else
                        {
                            $this->formatRecords['xfrecords'][] = array
                                (
                                'type' => 'other',
                                'format' => '',
                                'code' => $indexCode,
                                'formatCode' => $formatCode
                            );
                        }
                    }
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR:
                    $this->nineteenFour = (ord( $this->data[$pos + 4] ) == 1);
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET:
                    $rec_offset = $this->_GetInt4d( $this->data, $pos + 4 );
                    $rec_typeFlag = ord( $this->data[$pos + 8] );
                    $rec_visibilityFlag = ord( $this->data[$pos + 9] );
                    $rec_length = ord( $this->data[$pos + 10] );

                    if ( $version == SPREADSHEET_EXCEL_READER_BIFF8 )
                    {
                        $chartype = ord( $this->data[$pos + 11] );
                        if ( $chartype == 0 )
                        {
                            $rec_name = iconv('ASCII', "$this->_defaultEncoding//TRANSLIT", substr( $this->data, $pos + 12, $rec_length ) );
                        }
                        else
                        {
                            $rec_name = iconv( 'UTF-16LE', $this->_defaultEncoding . '//TRANSLIT', substr( $this->data, $pos + 12, $rec_length * 2 ) );
                        }
                    }
                    elseif ( $version == SPREADSHEET_EXCEL_READER_BIFF7 )
                    {
                        $sheetName = substr( $this->data, $pos + 11, $rec_length );
                        $rec_name = iconv($this->codePage, "$this->_defaultEncoding//TRANSLIT", $sheetName );
                    }
                    
                    $this->boundsheets[] = array( 'name' => $rec_name,
                        'offset' => $rec_offset );

                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_CODEPAGE:
                    $recordLength = ord( $this->data[$pos + 2] ) | ord( $this->data[$pos + 3] ) << 8;
                    $codePage = substr( $this->data, $pos + 4, $recordLength );
                    $codePage = ord( $codePage[0] ) | ord( $codePage[1] ) << 8;
                    $this->codePage = PHPExcel_Shared_CodePage::NumberToName( $codePage );
                    break;
                default:
            }

            $pos += $length + 4;
            $code = ord( $this->data[$pos] ) | ord( $this->data[$pos + 1] ) << 8;
            $length = ord( $this->data[$pos + 2] ) | ord( $this->data[$pos + 3] ) << 8;
        }

        $key = 0;
        foreach ( $this->boundsheets as $val )
        {
            $this->sn = $key;
            if($this->_parsesheet( $val ) == PARSESHEET_SUCCESS)
                $key++;//Индекс увеличиваем только для WorkSheet
        }
        return true;
    }

    /**
     * Parse a worksheet
     *
     * @access private
     * @param todo
     * @todo fix return codes
     */
    function _parsesheet( $val )
    {
        $spos = $val['offset'];
        $cont = true;
        // read BOF
        $code = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
        $length = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;

        $version = ord( $this->data[$spos + 4] ) | ord( $this->data[$spos + 5] ) << 8;
        $substreamType = ord( $this->data[$spos + 6] ) | ord( $this->data[$spos + 7] ) << 8;

        if ( ($version != SPREADSHEET_EXCEL_READER_BIFF8) && ($version != SPREADSHEET_EXCEL_READER_BIFF7) )
        {
            return PARSESHEET_WRONG_BIFF_VERSION;
        }

        if ( $substreamType != SPREADSHEET_EXCEL_READER_WORKSHEET )
        {
            return PARSESHEET_NOT_WORKSHEET;
        }
        $spos += $length + 4;
        while ( $cont )
        {
            $lowcode = ord( $this->data[$spos] );
            if ( $lowcode == SPREADSHEET_EXCEL_READER_TYPE_EOF )
                break;
            $code = $lowcode | ord( $this->data[$spos + 1] ) << 8;
            $length = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
            $spos += 4;
            $this->sheets[$this->sn]['maxrow'] = $this->_rowoffset - 1;
            $this->sheets[$this->sn]['maxcol'] = $this->_coloffset - 1;
            unset( $this->rectype );
            $this->multiplier = 1; // need for format with %
            switch ( $code )
            {
                case SPREADSHEET_EXCEL_READER_TYPE_DIMENSION:
                    if ( !isset( $this->numRows ) )
                    {
                        if ( ($length == 10) || ($version == SPREADSHEET_EXCEL_READER_BIFF7) )
                        {
                            $this->sheets[$this->sn]['numRows'] = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                            $this->sheets[$this->sn]['numCols'] = ord( $this->data[$spos + 6] ) | ord( $this->data[$spos + 7] ) << 8;
                        }
                        else
                        {
                            $this->sheets[$this->sn]['numRows'] = ord( $this->data[$spos + 4] ) | ord( $this->data[$spos + 5] ) << 8;
                            $this->sheets[$this->sn]['numCols'] = ord( $this->data[$spos + 10] ) | ord( $this->data[$spos + 11] ) << 8;
                        }
                    }
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS:
                    $cellRanges = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    for ( $i = 0; $i < $cellRanges; $i++ )
                    {
                        $fr = ord( $this->data[$spos + 8 * $i + 2] ) | ord( $this->data[$spos + 8 * $i + 3] ) << 8;
                        $lr = ord( $this->data[$spos + 8 * $i + 4] ) | ord( $this->data[$spos + 8 * $i + 5] ) << 8;
                        $fc = ord( $this->data[$spos + 8 * $i + 6] ) | ord( $this->data[$spos + 8 * $i + 7] ) << 8;
                        $lc = ord( $this->data[$spos + 8 * $i + 8] ) | ord( $this->data[$spos + 8 * $i + 9] ) << 8;
                        if ( $lr - $fr > 0 )
                        {
                            $this->sheets[$this->sn]['cellsInfo'][$fr + 1][$fc + 1]['rowspan'] = $lr - $fr + 1;
                        }
                        if ( $lc - $fc > 0 )
                        {
                            $this->sheets[$this->sn]['cellsInfo'][$fr + 1][$fc + 1]['colspan'] = $lc - $fc + 1;
                        }
                    }
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_RK:
                case SPREADSHEET_EXCEL_READER_TYPE_RK2:
                    //notify( "TYPE_RK" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $rknum = $this->_GetInt4d( $this->data, $spos + 6 );
                    $numValue = $this->_GetIEEE754( $rknum );
                    $this->getFormatCode( $spos );
                    if ( $this->isDate( $spos ) )
                    {
                        list($string, $raw) = $this->createDate( $numValue );
                    }
                    else
                    {
                        $raw = $numValue;
                        if ( isset( $this->_columnsFormat[$column + 1] ) )
                        {
                            $this->curformat = $this->_columnsFormat[$column + 1];
                        }
                        $string = sprintf( $this->curformat, $numValue * $this->multiplier );
                    }
                    $this->addcell( $row, $column, $string, $numValue );
                    //notify( "Values row:[$row] col:[" . PHPExcel_Cell::stringFromColumnIndex( $column ) . "] default:[$string] source:[$numValue]" );
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_LABELSST:
                    //notify( "LABELSST" );
                    //echo 'data SST<br>';
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $xfindex = ord( $this->data[$spos + 4] ) | ord( $this->data[$spos + 5] ) << 8;
                    //$this->getFormatCode( $spos );
                    $this->formatCode = 'GENERAL';
                    $index = $this->_GetInt4d( $this->data, $spos + 6 );
                    $string = $this->sst[$index];
                    $this->addcell( $row, $column, $this->sst[$index], $string );
                    //notify( "Values row:[$row] col:[$column] default:[{$this->sst[$index]}] source:[$string]" );
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_MULRK:
                    //notify( "MULRK" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $colFirst = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $colLast = ord( $this->data[$spos + $length - 2] ) | ord( $this->data[$spos + $length - 1] ) << 8;
                    $columns = $colLast - $colFirst + 1;
                    $tmppos = $spos + 4;

                    for ( $i = 0; $i < $columns; $i++ )
                    {

                        $numValue = $this->_GetIEEE754( $this->_GetInt4d( $this->data, $tmppos + 2 ) );
                        $this->getFormatCode( $tmppos - 4 );
                        if ( $this->isDate( $tmppos - 4 ) )
                        {
                            list($string, $raw) = $this->createDate( $numValue );
                        }
                        else
                        {
                            $raw = $numValue;
                            if ( isset( $this->_columnsFormat[$colFirst + $i + 1] ) )
                            {
                                $this->curformat = $this->_columnsFormat[$colFirst + $i + 1];
                            }
                            $string = sprintf( $this->curformat, $numValue * $this->multiplier );
                        }
                        $tmppos += 6;
                        $this->addcell( $row, $colFirst + $i, $string, $numValue );
                        //notify( "Values $row " . ($colFirst + $i) . " $string $numValue" );
                    }
                    //notify("MULRK $row ".($colFirst + $i)." $string");
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_NUMBER:
                    //notify( "NUMBER" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $tmp = unpack( "ddouble", substr( $this->data, $spos + 6, 8 ) ); // It machine machine dependent
                    $this->getFormatCode( $spos );
                    if ( $this->isDate( $spos ) )
                    {
                        list($string, $raw) = $this->createDate( $tmp['double'] );
                        $raw = $tmp['double'];
                    }
                    else
                    {
                        if ( isset( $this->_columnsFormat[$column + 1] ) )
                        {
                            $this->curformat = $this->_columnsFormat[$column + 1];
                        }
                        $raw = $this->createNumber( $spos );
                        $string = sprintf( $this->curformat, $raw * $this->multiplier );
                    }
                    $this->addcell( $row, $column, $string, $raw );
                    //notify( "Values $row $column $string $raw" );
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_FORMULA:
                case SPREADSHEET_EXCEL_READER_TYPE_FORMULA2:
                    //notify( "FORMULA" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $this->getFormatCode( $spos );
                    if ( (ord( $this->data[$spos + 6] ) == 0) && (ord( $this->data[$spos + 12] ) == 255) && (ord( $this->data[$spos + 13] ) == 255) )
                    {
                        //notify( "String formula" );
                        //String formula. Result follows in a STRING record
                        //echo "FORMULA $row $column Formula with a string<br>\n";
                    }
                    elseif ( (ord( $this->data[$spos + 6] ) == 1) && (ord( $this->data[$spos + 12] ) == 255) && (ord( $this->data[$spos + 13] ) == 255) )
                    {
                        //notify("Boolean formula");
                        //Boolean formula. Result is in +2; 0=false,1=true
                    }
                    elseif ( (ord( $this->data[$spos + 6] ) == 2) && (ord( $this->data[$spos + 12] ) == 255) && (ord( $this->data[$spos + 13] ) == 255) )
                    {
                        //notify("Error formula");
                        //Error formula. Error code is in +2;
                    }
                    elseif ( (ord( $this->data[$spos + 6] ) == 3) && (ord( $this->data[$spos + 12] ) == 255) && (ord( $this->data[$spos + 13] ) == 255) )
                    {
                        //notify("Formula result is a null string.");
                        //Formula result is a null string.
                    }
                    else
                    {
                        // result is a number, so first 14 bytes are just like a _NUMBER record
                        $tmp = unpack( "ddouble", substr( $this->data, $spos + 6, 8 ) ); // It machine machine dependent
                        if ( $this->isDate( $spos ) )
                        {
                            list($string, $raw) = $this->createDate( $tmp['double'] );
                            $raw = $tmp['double'];
                        }
                        else
                        {
                            if ( isset( $this->_columnsFormat[$column + 1] ) )
                            {
                                $this->curformat = $this->_columnsFormat[$column + 1];
                            }
                            $raw = $this->createNumber( $spos );
                            $string = sprintf( $this->curformat, $raw * $this->multiplier );
                        }
                        $this->addcell( $row, $column, $string, $raw );
                        //notify( "Values $row $column $string $raw" );
                    }
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_BOOLERR:
                    //notify( "Type_BOOLERR" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    $this->getFormatCode( $spos );
                    $string = ord( $this->data[$spos + 6] );
                    $isError = ord( $this->data[$spos + 7] );
                    $value = $isError ? $this->mapErrorCode( $string ) : (bool) $string;
                    $this->addcell( $row, $column, $string, $value );
                    //notify( "Values $row $column $string $value" );
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_ROW:
                case SPREADSHEET_EXCEL_READER_TYPE_DBCELL:
                case SPREADSHEET_EXCEL_READER_TYPE_MULBLANK:
                    break;
                case SPREADSHEET_EXCEL_READER_TYPE_LABEL:
                    //notify( "LABEL" );
                    $row = ord( $this->data[$spos] ) | ord( $this->data[$spos + 1] ) << 8;
                    $column = ord( $this->data[$spos + 2] ) | ord( $this->data[$spos + 3] ) << 8;
                    //$this->getFormatCode( $spos );
                    $this->formatCode = 'GENERAL';
                    $numChars = ord( $this->data[$spos + 6] ) | ord( $this->data[$spos + 7] ) << 8;

                    if ( $this->codePage == 'CP1251' )
                    {
                        $label = substr( $this->data, $spos + 8, $numChars );
                        $string = iconv($this->codePage, "$this->_defaultEncoding//TRANSLIT", $label);
                    }
                    else
                    {
                        if ( ord( $this->data[$spos + 8] ) == 0 )
                        {
                            $label = substr( $this->data, $spos + 9, $numChars );
                            $string = $label;
                        }
                        else
                        {
                            $label = substr( $this->data, $spos + 9, $numChars * 2 );
                            $string = iconv($this->codePage, "$this->_defaultEncoding//TRANSLIT", $label);
                        }
                    }
                    //$string = iconv($this->codePage, "$this->_defaultEncoding//TRANSLIT", $label);
                    $this->addcell( $row, $column, $label, $string );
                    //notify( "Values $row $column default:$label source:$string" );
                    break;

                case SPREADSHEET_EXCEL_READER_TYPE_EOF:
                    $cont = false;
                    break;
                default:
                    break;
            }
            $spos += $length;
        }

        if ( !$this->sheets[$this->sn]['numRows'] )
            $this->sheets[$this->sn]['numRows'] = $this->sheets[$this->sn]['maxrow']
                ? $this->sheets[$this->sn]['maxrow']
                : count($this->sheets[$this->sn]['cells']);

        if ( !isset( $this->sheets[$this->sn]['numCols'] ) )
            $this->sheets[$this->sn]['numCols'] = $this->sheets[$this->sn]['maxcol'];
        
        if ( isset( $this->sheets[$this->sn] ) )
            $this->sheets[$this->sn]['title'] = $val['name'];

        return PARSESHEET_SUCCESS;
    }

    /**
     * Check whether the current record read is a date
     *
     * @param todo
     * @return boolean True if date, false otherwise
     */
    function isDate( $spos )
    {
        $xfindex = $this->xfindex;
        if ( $this->formatRecords['xfrecords'][$xfindex]['type'] == 'date' )
        {
            $this->curformat = $this->formatRecords['xfrecords'][$xfindex]['format'];
            $this->rectype = 'date';
            return true;
        }
        else
        {
            if ( $this->formatRecords['xfrecords'][$xfindex]['type'] == 'number' )
            {
                $this->curformat = $this->formatRecords['xfrecords'][$xfindex]['format'];
                $this->rectype = 'number';
                if ( ($xfindex == 0x9) || ($xfindex == 0xa) )
                {
                    $this->multiplier = 100;
                }
            }
            else
            {
                $this->curformat = $this->_defaultFormat;
                $this->rectype = 'unknown';
            }
            return false;
        }
    }

    /**
     * Convert the raw Excel date into a human readable format
     *
     * Dates in Excel are stored as number of seconds from an epoch.  On
     * Windows, the epoch is 30/12/1899 and on Mac it's 01/01/1904
     *
     * @access private
     * @param integer The raw Excel value to convert
     * @return array First element is the converted date, the second element is number a unix timestamp
     */
    function createDate( $numValue )
    {
        if ( $numValue > 1 )
        {
            $utcDays = $numValue - ($this->nineteenFour ? SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904 : SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS);
            $utcValue = round( ($utcDays + 1) * SPREADSHEET_EXCEL_READER_MSINADAY );
            $string = date( $this->curformat, $utcValue );
            $raw = $utcValue;
        }
        else
        {
            $raw = $numValue;
            $hours = floor( $numValue * 24 );
            $mins = floor( $numValue * 24 * 60 ) - $hours * 60;
            $secs = floor( $numValue * SPREADSHEET_EXCEL_READER_MSINADAY ) - $hours * 60 * 60 - $mins * 60;
            $string = date( $this->curformat, mktime( $hours, $mins, $secs ) );
        }

        return array( $string, $raw );
    }

    function createNumber( $spos )
    {
        $rknumhigh = $this->_GetInt4d( $this->data, $spos + 10 );
        $rknumlow = $this->_GetInt4d( $this->data, $spos + 6 );
        $sign = ($rknumhigh & 0x80000000) >> 31;
        $exp = ($rknumhigh & 0x7ff00000) >> 20;
        $mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
        $mantissalow1 = ($rknumlow & 0x80000000) >> 31;
        $mantissalow2 = ($rknumlow & 0x7fffffff);
        $value = $mantissa / pow( 2, (20 - ($exp - 1023) ) );
        if ( $mantissalow1 != 0 )
            $value += 1 / pow( 2, (21 - ($exp - 1023) ) );
        $value += $mantissalow2 / pow( 2, (52 - ($exp - 1023) ) );
        if ( $sign )
        {
            $value = -1 * $value;
        }
        return $value;
    }

    function addcell( $row, $col, $string, $raw = '' )
    {
        $this->sheets[$this->sn]['maxrow'] = max( $this->sheets[$this->sn]['maxrow'], $row + $this->_rowoffset );
        $this->sheets[$this->sn]['maxcol'] = max( $this->sheets[$this->sn]['maxcol'], $col + $this->_coloffset );
        $this->sheets[$this->sn]['cells'][$row + $this->_rowoffset][$col + $this->_coloffset] = $string;
        $this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['raw'] = $raw;
        if ( isset( $this->rectype ) )
            $this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['type'] = $this->rectype;

        $this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['format'] = $this->formatCode != '' ? $this->formatCode : "GENERAL";
    }

    function _GetIEEE754( $rknum )
    {
        if ( ($rknum & 0x02) != 0 )
        {
            $value = $rknum >> 2;
        }
        else
        {
//mmp
// first comment out the previously existing 7 lines of code here
//                $tmp = unpack("d", pack("VV", 0, ($rknum & 0xfffffffc)));
//                //$value = $tmp[''];
//                if (array_key_exists(1, $tmp)) {
//                    $value = $tmp[1];
//                } else {
//                    $value = $tmp[''];
//                }
// I got my info on IEEE754 encoding from
// http://research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
// The RK format calls for using only the most significant 30 bits of the
// 64 bit floating point value. The other 34 bits are assumed to be 0
// So, we use the upper 30 bits of $rknum as follows...
            $sign = ($rknum & 0x80000000) >> 31;
            $exp = ($rknum & 0x7ff00000) >> 20;
            $mantissa = (0x100000 | ($rknum & 0x000ffffc));
            $value = $mantissa / pow( 2, (20 - ($exp - 1023) ) );
            if ( $sign )
            {
                $value = -1 * $value;
            }
//end of changes by mmp
        }

        if ( ($rknum & 0x01) != 0 )
        {
            $value /= 100;
        }
        return $value;
    }

    function _GetInt4d( $data, $pos )
    {
        $value = ord( $data[$pos] ) | (ord( $data[$pos + 1] ) << 8) | (ord( $data[$pos + 2] ) << 16) | (ord( $data[$pos + 3] ) << 24);
        if ( $value >= 4294967294 )
        {
            $value = -2;
        }
        return $value;
    }

    function getFormatCode( $pos )
    {
        $xfindex = ord( $this->data[$pos + 4] ) | ord( $this->data[$pos + 5] ) << 8;
        $this->formatCode = $this->formatRecords['xfrecords'][$xfindex]['formatCode'];
        $this->xfindex = $xfindex;
        /*notify( "xfindex:[$xfindex] format:$this->formatCode" );
        notify( "code:[" . $this->formatRecords['xfrecords'][$xfindex]['code'] . "]" );
        notify( "format:[" . $this->formatRecords['xfrecords'][$xfindex]['format'] . "]" );*/
    }

    function mapErrorCode( $subData )
    {
        switch ( $subData )
        {
            case 0x00: return '#NULL!';
                break;
            case 0x07: return '#DIV/0!';
                break;
            case 0x0F: return '#VALUE!';
                break;
            case 0x17: return '#REF!';
                break;
            case 0x1D: return '#NAME?';
                break;
            case 0x24: return '#NUM!';
                break;
            case 0x2A: return '#N/A';
                break;
            default: return false;
        }
    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
