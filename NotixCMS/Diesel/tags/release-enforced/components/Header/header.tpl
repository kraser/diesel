<link rel="shortcut icon" href="{$headerModel->favicon}">
<meta content="text/html; charset={$headerModel->charset}" http-equiv="content-type">
<meta content="index, follow" name="robots">
<meta content="{$headerModel->keywords}" name="keywords">
<title>{$headerModel->title}</title>
{section name = cssItem loop = $headerModel->css}
<link type="text/css" href="{$headerModel->css[cssItem]}" rel="stylesheet">
{/section}
{section name = jsFile loop = $headerModel->js}
<script src="{$headerModel->js[jsFile]}" type="text/javascript"></script>
{/section}
<script type="text/javascript" language="javascript">
{$headerModel->script|default:''}
</script>