<?php

class Logo
{
    public static function render ( $createLink = true )
    {
        $title = "Нотикс - агентство интернет-маркетинга";
        $html = "<div id='notixLogo' style='display: inline-table;'><span id='notixDescription'>разработка сайта</span>";
        if ( $createLink )
        {
            $html .= "<a href='http://notix.su' target='_blank' >";
        }
        else
        {
            $html .= "<span style='padding-left: 5px; padding-right: 5px;'>http://notix.su</span>";
        }
        $html .= "<span id='notixLogotype'><img alt='$title' title='$title' src='/images/notixLogo.png' style='width: 20px; margin: 0 -1px -5px 5px; padding: 1px;'></span>";
        $html .= "<span id='notixName' style='font-size: larger;'>Нотикс</span>";
        if ( $createLink )
        {
            $html .= "</a>";
        }
        $html .= "</div>";

        return $html;
    }
}
