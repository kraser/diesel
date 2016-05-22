{$header=$model.header}
<meta content="text/html; charset={$header->charset}" http-equiv="Content-Type">
<title>{$header->title}</title>
{$header->metaText}
{foreach from=$header->js item=js}
<script type="text/javascript" src="{$js}"></script>
{/foreach}
<script type="text/javascript" src="/themes/dinas/js/tab.js"/>