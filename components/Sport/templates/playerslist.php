<?php
if ( !count ($players) )
    echo "Нема ничого";
else
{
    ?>
<table>
    <tr>
    <?php
    foreach ($columns as $key => $columnName)
    {
        ?>
        <td data-name="<?php echo $key; ?>"><?php echo $columnName; ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
    <?php
    foreach ($players as $player)
    {
        ?>
        <tr data-alias='<?php echo $team->alias; ?>'>
        <?php
        foreach ($columns as $key => $columnName)
        {
            $href = null;
            if ( $key == 'id')
                $href = $this->createLink() . "/player/id/$player->id";
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
    ?>
</table>
<?php
}
