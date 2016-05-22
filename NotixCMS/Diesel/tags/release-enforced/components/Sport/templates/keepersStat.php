<?php
if ( !count ( $keepers ) )
    echo "Нема ничого";
else
{
    ?>
<table>
    <?php
//    foreach ( $teams as $team )
//    {
        ?>
        <tr>
            <td colspan="<?php echo count ( $columns ); ?>"><b>Статистика игры вратарей</b></td>
        </tr>
    <tr>
    <?php
    foreach ( $columns as $key => $column )
    {
        ?>
        <td data-name="<?php echo $key; ?>" title="<?php echo $column['attr']['title']; ?>"><?php echo $column['colTitle']; ?></td>
        <?php
    }
    ?>
    </tr>
    <?php
    foreach ($keepers as $player)
    {
        ?>
        <tr>
        <?php
        foreach ($columns as $key => $column)
        {
            $href = null;
//            if ( $key == 'id')
//                $href = $this->createLink() . "/playerstat/id/$player->id";
//            if($key == 'name')
//                $href = $this->createLink() . "/teamStat/teamId/$team->id";
            ?>
            <td data-name="<?php echo $key; ?>"><?php
            if ( $href )
            {
                ?><a href='<?php echo $href; ?>'><?php
            }
            echo $player->$key;
            if ( $href )
            {
                ?></a><?php
            }
            ?></td>
            <?php
        }
        ?>
        </tr>
        <?php
    }
  //  }
    ?>






</table>
<?php
}
