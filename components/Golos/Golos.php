<?php

/**
 * Description of Golos
 *
 * @author knn
 */
class Golos extends CmsModule
{
    /* !!!!!!!!!!!! Всё переделать как в модуле */
    private $tmpDir = "tmp";
    private $data, $current, $host, $tmpPath, $url,
        $fullContentClassPath, $contentClassPath, $imgPath;
    private $mainContentFile = 'golosMain';
    private $actionContentFile = 'golosAction';
    private $table = 'golos';

    /**
     * Конструктор модуля
     */
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        if ( !session_id () )
        {
            session_start ();
        }
        $this->data = Starter::app ()->data;
        $this->contentClassPath = '/themes/' . Starter::app ()->theme . '/templates/modules/' . __CLASS__ . '/';
        $this->fullContentClassPath = DOCROOT . $this->contentClassPath;
        $this->imgPath = '/themes/' . Starter::app ()->theme . '/images/';
        $this->tmpPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->tmpDir . "/";

        $this->host = $_SERVER['HTTP_HOST'];
        $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];
        $this->url = $this->host . $request;

        $this->current = $this->loadLastEnabled ();
    }

    public function getContentClassPath ()
    {
        return $this->contentClassPath;
    }

    function loadLastEnabled ()
    {
        $result = false;
        //$records = $this->data->GetData ( 'golos', "AND `enabled`=0" );

        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (g.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT g.*" . $select
        . " FROM `prefix_" . $this->table . "` AS g"
        . $join
        . " WHERE g.`enabled` = '0'" . $where;
        $records = SqlTools::selectRows($sql, MYSQL_ASSOC);
        foreach ( $records as $record )
        {
            $result = $record; // должна быть только одна, последняя, запись
        }
        return $result;
    }

    function getContent ()
    {
        $isAction = $this->isAction ();
        $contentFile = ($isAction ? $this->actionContentFile : $this->mainContentFile);
        $contentPathFile = $this->fullContentClassPath . $contentFile . '.php';
        $content = "";

        if ( $isAction )
        {
            $content = $this->prepareActionPage ( $contentPathFile ); // golosAction.php
        }
        else
        {
            $content = $this->prepareMainPage ( $contentPathFile ); // golosMain.php
        }
        return $content;
    }

    function prepareMainPage ( $contentPathFile )
    {
        $golos_url = $this->url;
        if ( $this->current )
        {
            $golos_id = $this->current["golos_id"];
            $name = $this->current["name"];
            $golos_quest = $this->current["quest"];
            $golos_answer = $this->current["answers"];
        }
        else
        {
            $golos_id = "golos_id";
            $name = "name";
            $golos_quest = array ( "1", "2", "3" );
            $golos_answer = "answers";
        }
        $imagesPath = $this->imgPath;
        $result = include $contentPathFile;
        return $result;
    }

    function prepareActionPage ( $contentPathFile )
    {
        $answer = $_POST['answer'];
        $golos_id1 = $_POST['golos_id1'];
        $golos_answer1 = $_POST['golos_answer1'];
        $golos_quest1 = $_POST['golos_quest1'];

        // запись результата текущего голосования
        if ( $golos_answer1 == '' )
        { // разбор, если не было ещё ответов
            $golos_quest1 = explode ( '', $golos_quest1 );
            $in1 = 0;
            for ( $i = 0; $i < sizeof ( $golos_quest1 ); $i++ )
            {
                if ( $in1 != 0 )
                {
                    $golos_answer1.='^';
                }
                $in1 = 1;
                if ( $answer == $i )
                {
                    $golos_answer1.='1';
                }
                else
                {
                    $golos_answer1.='0';
                }
            }
        }
        else
        { // добавление к существующим ответам
            $golos_answer1 = explode ( '^', $golos_answer1 );
            $golos_answer1[$answer] ++;
            $str = '';
            $in1 = 0;
            for ( $i = 0; $i < sizeof ( $golos_answer1 ); $i++ )
            {
                if ( $in1 != 0 )
                {
                    $str.='^';
                }
                $in1 = 1;
                $str.=$golos_answer1[$i];
            }
            $golos_answer1 = $str;
        }

        if ( !empty ( $golos_id1 ) && empty ( $GLOBALS["post_golos" . $golos_id1] ) )
        { // выставляется кука голосования
            SetCookie ( "post_golos" . $golos_id1, 'true', time () + 60480000 );
            $sql = "UPDATE `prefix_golos` SET `golos_answer`='$golos_answer1' WHERE `golos_id`=$golos_id1"; //??
            SqlTools::execute ( $sql );
        }

        // переменные для вывода списка
        $forDiagram = $this->data->GetData ( "golos", "", "`golos_id` DESC" );

        $imagesPath = $this->imgPath;
        $result = include $contentPathFile;
        return $result;
    }

    function isAction ()
    {
        $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];
        $pathStack = array_filter ( explode ( '/', $request ) );
        $last = end ( $pathStack );

        return ($last == "action");
    }

    /**
     * Метод вызываемый для работы модуля
     */
    function Run ()
    {
        return tpl ( 'modules/' . __CLASS__ . '/mainpage', array (
            'name' => $this->currentDocument->title,
            'title' => $this->currentDocument->title . ' — ' . Sterter::app ()->title
        ) );
    }

    function getColor ( $hex )
    {
        $hex = substr ( $hex, 1, strlen ( $hex ) - 1 );
        $result->red = hexdec ( substr ( $hex, 0, 2 ) );
        $result->green = hexdec ( substr ( $hex, 2, 2 ) );
        $result->blue = hexdec ( substr ( $hex, 4, 2 ) );
        return $result;
    }

    function getRandColor ()
    {
        $result->red = rand ( 0, 255 );
        $result->green = rand ( 0, 255 );
        $result->blue = rand ( 0, 255 );
        return $result;
    }

    function getRandColor2 ()
    {
        $s = rand ( 0, 255 );
        $result = '#' . dechex ( floor ( $s / 16 ) ) . dechex ( $s % 16 );
        $s = rand ( 0, 255 );
        $result = $result . dechex ( floor ( $s / 16 ) ) . dechex ( $s % 16 );
        $s = rand ( 0, 255 );
        $result = $result . dechex ( floor ( $s / 16 ) ) . dechex ( $s % 16 );
        return $result;
    }

    function drawGauge ( $iw, $ih, $par, $numbers, $addon, $font )
    {
        $image = ImageCreate ( $iw, $ih );
        $c['black'] = ImageColorAllocate ( $image, 0, 0, 0 );
        $c['white'] = ImageColorAllocate ( $image, 255, 255, 255 );
        $c['trans'] = ImageColorAllocate ( $image, 254, 254, 254 );
        $c['bg'] = ImageColorAllocate ( $image, 238, 202, 77 );
        for ( $i = 0; $i < count ( $par ); $i++ )
        {
            if ( !empty ( $par[$i]['color'] ) )
            {
                $tmp = $this->getColor ( $par[$i]['color'] );
            }
            else
            {
                $tmp = $this->getRandColor ();
            }

            $par[$i]['color'] = ImageColorAllocate ( $image, $tmp->red, $tmp->green, $tmp->blue );
            $par[$i]['invers'] = ImageColorAllocate ( $image, 255 - $tmp->red, 255 - $tmp->green, 255 - $tmp->blue );
        }
        imagecolortransparent ( $image, $c['trans'] );
        ImageFill ( $image, 0, 0, $c['trans'] ); //BG
        $angle = 0;
        for ( $i = 0; $i < count ( $par ); $i++ )
        {
            if ( $par[$i]['var'] != 0 )
            {
                $par1 = 360 / (100 / $par[$i]['var']);
            }
            else
            {
                $par1 = 0;
            }
            ImageFilledArc ( $image, $iw / 2, $ih / 2, $iw - 1, $ih - 1, $angle, $angle + $par1, $par[$i]['color'], $c['black'] );
            $angle+=$par1;
        }

        ImageFilledArc ( $image, $iw / 2, $ih / 2, $iw - 1 - $iw / 4, $ih - 1 - $ih / 4, 0, 360, $c['trans'], $c['black'] );
        $angle = 0;
        for ( $i = 0; $i < count ( $par ); $i++ )
        {
            if ( $par[$i]['var'] != 0 )
            {
                $par1 = 360 / (100 / $par[$i]['var']);
            }
            else
            {
                $par1 = 0;
            }
            ImageFilledArc ( $image, $iw / 2, $ih / 2, $iw - 1 - $iw / 4 - $iw / 20, $ih - 1 - $ih / 4 - $ih / 20, $angle, $angle + $par1, $par[$i]['color'], $c['black'] );
            $angle+=$par1;
        }

        $angle = 0;
        if ( $numbers )
        {
            for ( $i = 0; $i < count ( $par ); $i++ )
            {
                if ( $par[$i]['var'] != 0 )
                    $par1 = 360 / (100 / $par[$i]['var']);
                else
                    $par1 = 0;
                $x = ($iw / 2 - $iw / 4) * cos ( (($angle + $par1 / 2) * 2 * pi ()) / 360 ) + $iw / 2 - (ImageFontWidth ( $font ) * 3) / 2 + 1;
                $y = ($ih / 2 - $ih / 4) * sin ( (($angle + $par1 / 2) * 2 * pi ()) / 360 ) + $ih / 2 - ImageFontHeight ( $font ) / 2 + 1;
                ImageString ( $image, $font, $x, $y, floor ( $par[$i]['var'] ) . '%', $par[$i]['invers'] );
                $angle+=$par1;
            }
        }

        $i = 1;
        while ( file_exists ( "./" . $i . '.gif' ) )
        {
            $i++;
        }
        $rnd = $i;
        imagegif ( $image, $this->tmpPath . $rnd . '.gif' );
        chmod ( $this->tmpPath . $rnd . '.gif', 0777 );
        $width = ' width="' . $iw . '" ';
        $height = ' height="' . $ih . '" ';
        echo '<img src="' . $this->host . "/" . $this->tmpDir . "/" . $rnd . '.gif"' . $width . $height . ' ' . $addon . '>';
        ImageDestroy ( $image );
        $f = fopen ( $this->tmpPath . '1.txt', 'a' );
        fputs ( $f, "$rnd\n" );
        fclose ( $f );
    }

    public static function getInstaller ()
    {
        return new GolosInstaller();
    }
}
