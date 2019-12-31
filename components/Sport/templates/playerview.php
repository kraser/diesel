<?php
$image = $images[$player->id];
$img = Starter::app ()->imager->resize ( $image['src'], 300, 300, 'png' );
?>
<img src="<?php echo $img; ?>">
<div>Амплуа: <span><?php echo $player->amplua; ?></span></div>
<div>Дата рождения: <span><?php echo $player->birthdate; ?></span></div>
<div>Вес: <span><?php echo $player->weight; ?></span></div>
<div>Рост: <span><?php echo $player->height; ?></span></div>
<div>Статус: <span><?php echo $player->status; ?></span></div>
<div>Хват клюшки: <span><?php echo $player->grip; ?></span></div>
Статистика
<?php
if ( is_a ( $stata, 'CmsDataProvider' ) )
{
    $data = ArrayTools::head ( $stata->data );
}
?>
<table>
    <tr>
    <?php
    foreach ( $statColumns as $key => $column )
    {
        ?>
        <th title="<?php echo $column['attr']['title']; ?>"><?php echo $column['colTitle']; ?></th>
        <?php
    }
    ?>
    </tr>
    <tr>
    <?php
    foreach ( $statColumns as $key => $column )
    {
        ?>
        <td title="<?php echo $column['attr']['title']; ?>"><?php echo $data->$key; ?></th>
        <?php
    }
    ?>
    </tr>
</table>
<div><?php echo $player->description; ?></div>