<?php

/**
 * Возвращает указанный шаблон с подставленными данными из $vars
 *
 * друзья мои, это самый быстрый, мощный,
 * гибкий и удобный шаблонизатор
 * всех времен и народов для PHP,
 * если есть альтернативное мнение,
 * не поленитесь, похоливарьте на
 * http://forum.dustweb.ru/
 *
 * @param string $templateName Имя шаблона из папки /templates
 * @param array $vars Массив переменных для шаблона
 * @return string
 */
function tpl($templateName, $vars=array())
{
    extract ( $vars );
    ob_start ();

    $sdir = TEMPL . DS . Starter::app ()->getTheme ();

    if ( is_file ( $sdir . '/templates/' . $templateName . '.php' ) )
        include $sdir . '/templates/' . $templateName . '.php';
    else
        error ( 'Не найден шаблон <code style="font-weight:bold">' . $templateName . '.php</code>*' . $sdir . '/templates/' . $templateName . '.php' );

    $ret = ob_get_contents ();
    ob_end_clean ();

    //Подмена вставленных блоков и форм {block:block_name_or_id} {form:form-name-or-id}
    preg_match_all ( '/\{(block|form):([\w_-]+)\}/simx', $ret, $result, PREG_PATTERN_ORDER );
    if ( isset ( $result[0] ) && !empty ( $result ) )
    {
        for ( $i = 0, $c = count ( $result[0] ); $i < $c; $i++ )
        {
            if ( isset ( $result[1][$i] ) )
            {
                if ( $result[1][$i] == 'block' )
                {
                    $insert = block ( $result[2][$i], 1 );
                }
                else if ( $result[1][$i] == 'form' )
                {
                    $insert = Starter::app ()->getModule( "Content" )->form ( $result[2][$i], 1 );
                }
                $ret = str_replace ( $result[0][$i], $insert, $ret );
            }
        }
    }
    /*
      //Сжатие и склейка CSS и JS в файле parts/head.php
      if($templateName == 'parts/head') {
      timegen('minify');

      //Убираем комментарии и хаки (те что обычно для ie)
      $nocomment = preg_replace('/<!--.*?-->/si', '', $ret);

      //Находим и заменяем все «screen» css файлы на один сжатый
      preg_match_all('/<link.*href.*=.*"(.*\.css[^"]*)".*media="screen".*>/i', $nocomment, $result, PREG_PATTERN_ORDER);
      $allcssTime = @filemtime(DOCROOT."/css/allcss.css");
      if(!$allcssTime) $mustReBuild = true;
      else $mustReBuild = false;
      foreach ($result[1] as $csslink) {
      $existFileTime = @filemtime(DOCROOT.$csslink);
      if(!$existFileTime) continue;
      if($allcssTime < $existFileTime) $mustReBuild = true;
      }
      if($mustReBuild) require_once DOCROOT.'/system/lib/minifiers/cssmin.php';
      $allcss = ''; $c=0; $tc = count($result[0]);
      foreach ($result[0] as $k=>$linktag) {
      if(++$c == $tc) $ret = str_replace($linktag, '<link href="/css/allcss.css" rel="stylesheet" type="text/css" media="screen" />', $ret);
      else $ret = str_replace($linktag, '', $ret);
      if($mustReBuild) {
      $css = file_get_contents(DOCROOT.$result[1][$k]);
      $pathcss = pathinfo($result[1][$k]);
      $pathcss = $pathcss['dirname'];
      //Замена путей
      preg_match_all('/url *?\( *?["\']?(.*[^\'"])["\']?\)/i', $css, $url_result, PREG_PATTERN_ORDER);
      foreach ($url_result[0] as $urlk=>$urldef) {
      //Если путь относительный, меняем на абсолютный
      if($url_result[1][$urlk][0] != '/') {
      $css = str_replace($urldef, 'url('.$pathcss.'/'.$url_result[1][$urlk].')', $css);
      }
      }

      $allcss .= CssMin::minify($css)."\r\n";
      }
      }
      if($mustReBuild) {
      file_put_contents(DOCROOT."/css/allcss.css", $allcss);
      chmod(DOCROOT."/css/allcss.css", 0755);
      $gz = gzopen(DOCROOT."/css/allcss.css.gz",'w9');
      gzwrite($gz, $allcss);
      gzclose($gz);
      chmod(DOCROOT."/css/allcss.css.gz", 0755);
      }

      //Находим и заменяем все js файлы на один сжатый
      preg_match_all('/<script.*src="(.*\.js[^"]*)".*>/i', $nocomment, $result, PREG_PATTERN_ORDER);
      $alljsTime = @filemtime(DOCROOT."/js/alljs.js");
      if(!$alljsTime) $mustReBuild = true;
      else $mustReBuild = false;
      foreach ($result[1] as $jslink) {
      $existFileTime = @filemtime(DOCROOT.$jslink);
      if(!$existFileTime) continue;
      if($alljsTime < $existFileTime) $mustReBuild = true;
      }
      if($mustReBuild) require_once DOCROOT.'/system/lib/minifiers/jsmin.php';
      $alljs = ''; $c=0; $tc = count($result[0]);
      foreach ($result[0] as $k=>$scripttag) {
      if(++$c == $tc) $ret = str_replace($scripttag, '<script type="text/javascript" src="/js/alljs.js"></script>', $ret);
      else $ret = str_replace($scripttag, '', $ret);
      if($mustReBuild) {
      $js = file_get_contents(DOCROOT.$result[1][$k]);
      $alljs .= JSMin::minify($js)."\r\n";
      }
      }
      if($mustReBuild) {
      file_put_contents(DOCROOT."/js/alljs.js", $alljs);
      chmod(DOCROOT."/js/alljs.js", 0755);
      $gz = gzopen(DOCROOT."/js/alljs.js.gz",'w9');
      gzwrite($gz, $alljs);
      gzclose($gz);
      chmod(DOCROOT."/js/alljs.js.gz", 0755);
      }

      timegen('minify',1);
      }
     */
    return $ret;
}
