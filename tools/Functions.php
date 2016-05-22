<?php

/**
 * Разбиение на страницы
 * @param Array $items - массив для разбиения на страницы
 * @param Integer $items_onpage - число элементов на странице
 * @param Integer $count_show_pages - число отображаемых страниц
 * @return Array
 */
function Paging ( $items, $items_onpage = 3, $count_show_pages = 5 )
{
    $items_count = count ( $items );
    $pages_count = ceil ( $items_count / $items_onpage );
    $toPage = filter_input ( INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT );
    if ( isset ( $toPage ) )
        $page_current = abs ( ( int ) $toPage );
    else
        $page_current = 1;

    $items_from = ($page_current - 1) * $items_onpage;


    $url = '';
    $request = filter_input ( INPUT_SERVER, 'REQUEST_URI' );
    $params = array_filter ( explode ( '&', parse_url ( $request, PHP_URL_QUERY ) ) );
    // убираем page, если присутствует
    if ( is_array ( $params ) )
    {
        $arr = array ();
        foreach ( $params as $param )
        {
            if ( substr ( strtolower ( $param ), 0, 5 ) != 'page=' )
                $arr[] = $param;
        }
        if ( $arr )
            $url = implode ( "&", $arr );
    }
    $url_page = "?" . ($url ? $url . '&' : '') . 'page=';
    $url = $url_page . '1';

    $left = $page_current - 1;
    $right = $pages_count - $page_current;
    if ( $left < floor ( $count_show_pages / 2 ) )
        $start = 1;
    else
        $start = $page_current - floor ( $count_show_pages / 2 );

    $end = $start + $count_show_pages - 1;
    if ( $end > $pages_count )
    {
        $start -= ($end - $pages_count);
        $end = $pages_count;
        if ( $start < 1 )
            $start = 1;
    }

    $rendered = tpl ( 'parts/paging', array (
        'pages_count' => $pages_count,
        'page_current' => $page_current,
        'items_count' => $items_count,
        'items_from' => $items_from + 1,
        'items_to' => $items_onpage * $page_current > $items_count ? $items_count : $items_onpage * $page_current,
        'items_onpage' => $items_onpage,
        'url' => $url,
        'url_page' => $url_page,
        'count_show_pages' => $count_show_pages,
        'start' => $start,
        'end' => $end,
    ) );

    return array (
        'items' => array_slice ( $items, $items_from, $items_onpage ),
        'rendered' => $rendered
    );
}

/**
 * <pre>Выводит дерево элементов <b>$tree</b></pre>
 * @param Array $root <p>Массив ветвей дерева уровня <b>$level</b></p>
 * @param Integer $level <p>Уровень вевей (Номер рекурсии)</p>
 * @param String $classType <p>Имя базового класса стилей CSS</p>
 * @param Array $treePath <p>Путь (цепь) ветвей от корня до активной ветви</p>
 * @param Integer $openId <p>Id активной ветви. Используется для раскрытия ветви</p>
 * @return String <p>html текст дерева</p>
 */
function TreeUl ( $root, $level, $classType, $treePath, $openId = '' )
{
    $html = '';
    $class = 'level' . $level;
    if ( $level >= 1 )
    {
        $categoryClass = 'sub_' . $classType . '_list';
        $id = "id='sub_" . $classType . "_List" . $openId . "'";

        if ( count ( $treePath ) && ($level + 1) < count ( $treePath['id'] ) )
        {
            if ( ($openId) && $openId == $treePath['id'][$level + 1] )
            {
                $style = "style='display: block;'";
            }
            else
            {
                $style = "style='display: none;'";
            }
        }
        else
        {
            $style = "style='display: none;'";
        }
    }
    else
    {
        $categoryClass = "index_" . $classType . "_list";
        $id = "id='index_" . $classType . "_List'";
        $style = "";
    }
    $html .= "<ul " . $id . " class='$categoryClass' $style>";
    foreach ( $root as $branch )
    {
        if ( count ( $branch->subCategories ) )
        {
            $html .= "<li class='sub_item' id='$branch->id'><a class='hide' >" . $branch->name . "</a></li>";
            $html .= TreeUl ( $branch->subCategories, $level + 1, $classType, $treePath, $branch->id );
            $html .= "</li>";
        }
        else
        {
            $html .= "<li class='sub_item'><a href='" . $branch->link . "'>" . $branch->name . "</a></li>";
        }
    }
    $html .= "</ul>";

    return $html;
}
