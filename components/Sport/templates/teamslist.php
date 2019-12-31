<?php
if ( !count ($teams) )
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
    foreach ($teams as $team)
    {
        ?>
        <tr data-alias='<?php echo $team->alias; ?>'>
        <?php
        foreach ($columns as $key => $columnName)
        {
            $href = null;
//            if ( $key == 'id')
//                $href = $this->createLink() . "/players/teamId/$team->id";
            if($key == 'name')
                $href = "/shl/playerstat/teamId/$team->id";
            ?>
            <td data-name="<?php echo $key; ?>"><?php
            if ( $href )
            {
                ?><a href='<?php echo $href; ?>'><?php
            }
            echo $team->$key;
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
