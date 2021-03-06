<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CmsController
 *
 * @author kraser
 */
class CmsController extends CmsComponent
{
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $this->module = [];
    }

    private $template;
    public function setTemplate ( $template )
    {
        $this->template = $template;
    }

    public function getTemplate ()
    {
        return $this->template;
    }

    public function beforeRender ()
    {
        return true;
    }

    protected function render ( $template, $params = [], $return = true )
    {
        if ( $this->befoRerender () )
        {
            $content = $this->renderPart ( $template, $params );
            $output = $this->output ( $this->template, [ 'content' => $content, 'title' => $this->title ] );
            if ( $return)
                return $output;
            else
                echo $output;
        }
    }

    protected function renderPart ( $template, $params = [], $return = true )
    {
        $output = $this->output ( $template, $params );
        if ( $return )
            return $output;
        else
            echo $output;
    }

    private function output ( $template, $params )
    {
        $templateFile = $this->resolveTemplateFile ( $template );
        if ( file_exists ( $templateFile . ".tpl" ) )
        {
            $smarty = SmartyTools::getSmarty ();
            $smarty->assign ( "this", $this );
            foreach ( $params as $varName => $varvalue )
            {
                $smarty->assign ( $varName, $varvalue );
            }
            $output = $smarty->fetch ( $templateFile . ".tpl" );
        }
        else
        {
            extract ( $params );
            ob_start ();
            include $templateFile . EXT;
            $output = ob_get_contents ();
            ob_end_clean ();
        }
        $html = $this->replaceCustomTags ( $output );

        return $html;
    }

    private function resolveTemplateFile ( $templateName )
    {
        $moduleName = get_class ( $this );
        $parts = array_filter ( explode ( '/', $templateName ) );


        $customPath = count ( $parts ) == 1 ? $moduleName . DS : "";
        $themeRoot = Starter::getAliasPath ( 'site' ) . DS . Starter::app ()->theme;
        $themePath = $themeRoot . DS . "templates" . DS . $customPath;
        $componentPath = $moduleName ? Starter::getAliasPath ( $moduleName ) . DS . "templates" . DS : "";

        if ( defined ( "MODE" ) && MODE == "Admin" )
            $templateFile = DOCROOT . DS . 'admin/tpls/' . $templateName;
        else if ( file_exists ( $themePath . $templateName . ".tpl" ) || file_exists ( $themePath . $templateName . EXT ) )
            $templateFile = $themePath . $templateName;
        else if ( file_exists ( $componentPath . $templateName . ".tpl" ) || file_exists ( $componentPath . $templateName . EXT ) )
            $templateFile = $componentPath . $templateName;
        else
            throw new Exception ( "Не найден шаблон <code style='font-weight:bold'>" . $templateName . "</code>" );

        return $templateFile;
    }

    /**
     * <pre>Заменяет вставленные тэги блоков и форм {block:block_name_or_id} {form:form-name-or-id}</pre>
     * @param String $html
     */
    private function replaceCustomTags ( $html )
    {
        $matches = [];
        preg_match_all ( '/\{(block|form):([\w_-]+)\}/simx', $html, $matches, PREG_PATTERN_ORDER );
        if ( isset ( $matches[0] ) && !empty ( $matches ) )
        {
            for ( $i = 0, $c = count ( $matches[0] ); $i < $c; $i++ )
            {
                if ( isset ( $matches[1][$i] ) )
                {
                    if ( $matches[1][$i] == 'block' )
                        $replacement = $this->widget ( "BlockWidget", [ 'blockId' => $matches[2][$i] ], true );
                    else if ( $matches[1][$i] == 'form' )
                        $replacement = $this->widget ( "FormWidget", [ 'formId' => $matches[2][$i] ], true );

                    $html = str_replace ( $matches[0][$i], $replacement, $html );
                }
            }
        }

        return $html;
    }
}