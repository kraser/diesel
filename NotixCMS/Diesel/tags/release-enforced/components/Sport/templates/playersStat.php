<?php
if ( !count ( $stata->data ) )
    echo "Ничего не найдено";
else
{
    $this->widget ( "PaginationWidget", [ 'paginator' => $stata->paginator ] );
    ?>
<table class="sortable">
    <tr>
    <?php
    foreach ( $columns as $key => $column )
    {
        ?>
        <th data-name="<?php echo $key; ?>" title="<?php echo $column['attr']['title']; ?>"><?php echo $column['colTitle']; ?></th>
        <?php
    }
    ?>
    </tr>
    <?php
    foreach ($stata->data as $player)
    {
        ?>
        <tr>
        <?php
        foreach ($columns as $key => $column)
        {
            $href = null;
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
?>