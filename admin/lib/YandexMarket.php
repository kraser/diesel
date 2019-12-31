<?php

class MarketFetcher
{
    public $error = false;
    public $error_text = '';

    private static function curl_populate_options ( &$h )
    {
        $user_agent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.3) Gecko/20090924 Ubuntu/9.10 (karmic) Firefox/3.5.3';
        curl_setopt ( $h, CURLOPT_USERAGENT, $user_agent );
        curl_setopt ( $h, CURLOPT_TIMEOUT, 15 );
        curl_setopt ( $h, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $h, CURLOPT_AUTOREFERER, true );
        curl_setopt ( $h, CURLOPT_REFERER, 'http://market.yandex.ru/' );
    }
    /* Отсылает запрос на поиск на Яндекс.Маркете */

    private static function market_get_list ( $search_str )
    {
        $h = curl_init ();
        self::curl_populate_options ( $h );
        curl_setopt ( $h, CURLOPT_URL, 'http://market.yandex.ru/search.xml?text=' . urlencode ( $search_str ) );
        $html = curl_exec ( $h );
        curl_close ( $h );

        return $html;
    }
    /* Парсит запрос на Яндекс.Маркете */

    private static function market_parse_list ( $html )
    {
        $dom = new DOMDocument;
        @$dom->loadHTML ( $html );
        $xpath = new DOMXPath ( $dom );
        $list = $xpath->query ( "//a/@href[starts-with(.,'/model.xml')]" );
        return @$list->item ( 0 )->textContent;
    }
    /* Запрашивает страницу с инфо о товаре */

    private static function market_get_info ( $url )
    {
        $h = curl_init ();
        self::curl_populate_options ( $h );
        curl_setopt ( $h, CURLOPT_URL, $url );
        $html = curl_exec ( $h );
        curl_close ( $h );

        return $html;
    }

    /**
     * Парсит страничку с инфо о товаре.
     * Возвращает ассоциативный массив (поле/значение)
     */
    private static function market_parse_info ( $html )
    {
        $dom = new DOMDocument;
        @$dom->loadHTML ( $html );

        $k = array ();
        $v = array ();

        $xpath = new DOMXPath ( $dom );
        $list = $xpath->query ( "//div[@id='full-spec-cont']/table/tbody/tr/td[@class='label']/span" );
        for ( $i = 0; $i <= $list->length; $i++ )
        {
            $k[] = @$list->item ( $i )->textContent;
        }
        $list = $xpath->query ( "//div[@id='full-spec-cont']/table/tbody/tr/td[@class='label']/parent::node()/td[2]" );
        for ( $i = 0; $i <= $list->length; $i++ )
        {
            $v[] = @$list->item ( $i )->textContent;
        }
        return array_combine ( $k, $v );
    }

    private static function market_parse_pics ( $html )
    {
        $dom = new DOMDocument;
        @$dom->loadHTML ( $html );

        $xpath = new DOMXPath ( $dom );
        $list = $xpath->query ( "//td[@class='bigpic']//a/@href | //td[@class='smallpic']//a/@href" );
        $res = array ();
        for ( $i = 0; $i < $list->length; $i++ )
        {
            $res[] = $list->item ( $i )->textContent;
        }
        return $res;
    }

    public function query ( $brand, $product )
    {
        $list = self::market_get_list ( $brand . ' ' . $product );
        if ( !$list )
        {
            $this->error = true;
            $this->error_text = 'Ошибка при поиске';
            return false;
        }
        sleep ( 1 );
        $url = self::market_parse_list ( $list );
        if ( !$url )
        {
            $product_ss = str_replace ( ' ', '', $product );
            if ( $product != $product_ss )
            {
                sleep ( 1 );
                $list = self::market_get_list ( $brand . ' ' . $product_ss );
                if ( !$list )
                {
                    $this->error = true;
                    $this->error_text = 'Ошибка при поиске';
                    return false;
                }
                sleep ( 1 );
                $url = self::market_parse_list ( $list );
                if ( !$url )
                {
                    $this->error = true;
                    $this->error_text = 'Товар не найден';
                    return false;
                }
            }
            else
            {
                $this->error = true;
                $this->error_text = 'Товар не найден';
                return false;
            }
        }
        sleep ( rand ( 2, 5 ) );
        $info = self::market_get_info ( 'http://market.yandex.ru' . $url );
        if ( !$info )
        {
            $this->error = true;
            $this->error_text = 'Ошибка при получении инфо о товаре';
            return false;
        }
        if ( isset ( $_GET['pics'] ) )
        {
            $data = self::market_parse_pics ( $info );
        }
        else
        {
            $data = self::market_parse_info ( $info );
        }
        if ( !$data )
        {
            $this->error = true;
            $this->error_text = 'В информации о товаре нет данных';
            return false;
        }
        if ( isset ( $_GET['pics'] ) )
        {
            $res['pics'] = $data;
            //$res['x'] = 'y';
            return $res;
        }

        $res['fields'] = array ();
        foreach ( $data as $k => $v )
        {
            if ( empty ( $k ) )
            {
                continue;
            }
            $v = str_replace ( "\n", "", $v );
            $res['fields'][] = array ( 'name' => addslashes ( $k ), 'value' => addslashes ( $v ) );
        }
        return $res;
    }
}
