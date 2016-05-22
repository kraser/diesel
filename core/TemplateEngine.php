<?php

/**
 * <pre>Возвращает указанный шаблон с подставленными данными из $vars</pre>
 */
class TemplateEngine
{
    /**
     * <pre>Базовый метод шаблонизатора
     *
     * Ищет шаблон в пользовательских темах или в шаблонах
     * компонента и возвращает html-текст с подставленными из модели данными
     * Если не передан компонент предоставивший модель данных, поиск будет
     * ограничен корнем шаблонов темы, для поиска в пользовательских шаблонах
     * модулей или в дефолтных шаблонах компонента необходимо
     * передать имя компонента
     * </pre>
     * @param String $templateName <p>Имя шаблона</p>
     * @param Mixed $model <p>Модель данных для шаблона</p>
     * @param String $componentName <p>Имя компонента предоставившего модель</p>
     * @param Boolean $useSmarty <p>Указание использвать Smarty</p>
     * @return String
     * @throws Exception
     */
    public static function view ( $templateName, $model = array (), $componentName = null, $useSmarty = false )
    {
        $ext = $useSmarty ? ".tpl" : EXT;
        $customCompPath = $componentName ? "modules" . DS . $componentName . DS : "";
        $templateRoot = TEMPL . DS . Starter::app ()->getTheme ();
        $templatePath = $templateRoot . DS . "templates" . DS . $customCompPath;
        $componentPath = $componentName ? Starter::getAliasPath ( $componentName ) . DS . "templates" . DS : "";

        if ( defined ( "MODE" ) && MODE == "Admin" )
            $templateName = DOCROOT . DS . 'admin/tpls/' . $templateName;
        else if ( file_exists ( $templatePath . $templateName . ".tpl" ) || file_exists ( $templatePath . $templateName . EXT ) )
            $templateName = $templatePath . $templateName;
        else if ( $componentPath && ( file_exists ( $componentPath . $templateName . ".tpl" ) || file_exists ( $componentPath . $templateName . EXT ) ) )
            $templateName = $componentPath . $templateName;
        else
            throw new Exception ( "Не найден шаблон <code style='font-weight:bold'>" . $templateName . $ext . "</code>" );

        if ( $useSmarty && file_exists ( $templateName . ".tpl" ) )
        {
            $smarty = SmartyTools::getSmarty ();
            foreach ( $model as $varName => $varvalue )
            {
                $smarty->assign ( $varName, $varvalue );
            }
            return $smarty->fetch ( $templateName . ".tpl" );
        }
        else
            return self::tpl ( $templateName, $model );
    }

    public static function tpl ( $templName, $vars = array () )
    {
        extract ( $vars );
        ob_start ();

        if ( is_file ( $templName . EXT ) )
            include $templName . EXT;
        else
            error ( 'Не найден шаблон <code style="font-weight:bold">' . $templName . '.php</code>' );

        $ret = ob_get_contents ();
        ob_end_clean ();

        if ( MODE == "Admin" )
            return $ret;

        //Подмена вставленных блоков и форм {block:block_name_or_id} {form:form-name-or-id}
        preg_match_all ( '/\{(block|form):([\w_-]+)\}/simx', $ret, $result, PREG_PATTERN_ORDER );
        if ( isset ( $result[0] ) && !empty ( $result ) )
        {
            for ( $i = 0, $c = count ( $result[0] ); $i < $c; $i++ )
            {
                if ( isset ( $result[1][$i] ) )
                {
                    if ( $result[1][$i] == 'block' )
                        $insert = block ( $result[2][$i], 1 );
                    else if ( $result[1][$i] == 'form' )
                        $insert = Starter::app ()->getModule ( "Content" )->form ( $result[2][$i], true );

                    $ret = str_replace ( $result[0][$i], $insert, $ret );
                }
            }
        }
        /*
          //Сжатие и склейка CSS и JS в файле parts/head.php
          if ( $templName == 'parts/head' )
          {
          timegen( 'minify' );

          //Убираем комментарии и хаки (те что обычно для ie)
          $nocomment = preg_replace( '/<!--.*?-->/si', '', $ret );

          //Находим и заменяем все «screen» css файлы на один сжатый
          preg_match_all( '/<link.*href.*=.*"(.*\.css[^"]*)".*media="screen".*>/i', $nocomment, $result, PREG_PATTERN_ORDER );
          $allcssTime = @filemtime( DOCROOT . "/css/allcss.css" );
          if ( !$allcssTime )
          $mustReBuild = true;
          else
          $mustReBuild = false;

          foreach ( $result[ 1 ] as $csslink )
          {
          $existFileTime = @filemtime( DOCROOT . $csslink );
          if ( !$existFileTime )
          continue;
          if ( $allcssTime < $existFileTime )
          $mustReBuild = true;
          }
          if ( $mustReBuild )
          require_once DOCROOT . '/system/lib/minifiers/cssmin.php';
          $allcss = '';
          $c = 0;
          $tc = count( $result[ 0 ] );
          foreach ( $result[ 0 ] as $k => $linktag )
          {
          if ( ++$c == $tc )
          $ret = str_replace( $linktag, '<link href="/css/allcss.css" rel="stylesheet" type="text/css" media="screen" />', $ret );
          else
          $ret = str_replace( $linktag, '', $ret );
          if ( $mustReBuild )
          {
          $css = file_get_contents( DOCROOT . $result[ 1 ][ $k ] );
          $pathcss = pathinfo( $result[ 1 ][ $k ] );
          $pathcss = $pathcss[ 'dirname' ];
          //Замена путей
          preg_match_all( '/url *?\( *?["\']?(.*[^\'"])["\']?\)/i', $css, $url_result, PREG_PATTERN_ORDER );
          foreach ( $url_result[ 0 ] as $urlk => $urldef )
          {
          //Если путь относительный, меняем на абсолютный
          if ( $url_result[ 1 ][ $urlk ][ 0 ] != '/' )
          {
          $css = str_replace( $urldef, 'url(' . $pathcss . '/' . $url_result[ 1 ][ $urlk ] . ')', $css );
          }
          }

          $allcss .= CssMin::minify( $css ) . "\r\n";
          }
          }
          if ( $mustReBuild )
          {
          file_put_contents( DOCROOT . "/css/allcss.css", $allcss );
          chmod( DOCROOT . "/css/allcss.css", 0755 );
          $gz = gzopen( DOCROOT . "/css/allcss.css.gz", 'w9' );
          gzwrite( $gz, $allcss );
          gzclose( $gz );
          chmod( DOCROOT . "/css/allcss.css.gz", 0755 );
          }

          //Находим и заменяем все js файлы на один сжатый
          preg_match_all( '/<script.*src="(.*\.js[^"]*)".*>/i', $nocomment, $result, PREG_PATTERN_ORDER );
          $alljsTime = @filemtime( DOCROOT . "/js/alljs.js" );
          if ( !$alljsTime )
          $mustReBuild = true;
          else
          $mustReBuild = false;
          foreach ( $result[ 1 ] as $jslink )
          {
          $existFileTime = @filemtime( DOCROOT . $jslink );
          if ( !$existFileTime )
          continue;
          if ( $alljsTime < $existFileTime )
          $mustReBuild = true;
          }
          if ( $mustReBuild )
          require_once DOCROOT . '/system/lib/minifiers/jsmin.php';
          $alljs = '';
          $c = 0;
          $tc = count( $result[ 0 ] );
          foreach ( $result[ 0 ] as $k => $scripttag )
          {
          if ( ++$c == $tc )
          $ret = str_replace( $scripttag, '<script type="text/javascript" src="/js/alljs.js"></script>', $ret );
          else
          $ret = str_replace( $scripttag, '', $ret );
          if ( $mustReBuild )
          {
          $js = file_get_contents( DOCROOT . $result[ 1 ][ $k ] );
          $alljs .= JSMin::minify( $js ) . "\r\n";
          }
          }
          if ( $mustReBuild )
          {
          file_put_contents( DOCROOT . "/js/alljs.js", $alljs );
          chmod( DOCROOT . "/js/alljs.js", 0755 );
          $gz = gzopen( DOCROOT . "/js/alljs.js.gz", 'w9' );
          gzwrite( $gz, $alljs );
          gzclose( $gz );
          chmod( DOCROOT . "/js/alljs.js.gz", 0755 );
          }

          timegen( 'minify', 1 );
          }
         */
        return $ret;
    }

    private function tplAdmin ( $templName, $vars )
    {
        extract ( $vars );
        ob_start ();
        require (DOCROOT . DS . 'admin/tpls/' . $templName . EXT);
        $ret = ob_get_contents ();
        ob_end_clean ();
        return $ret;
    }
}
