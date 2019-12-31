
<table class="sortable">
    <tr>
    <?php
    $regExp = '/\\/orderBy\\/([^\\/]+)/';
    $url = Starter::app ()->urlManager->getUrlPart ( 'path' );
    $query = Starter::app ()->urlManager->createRequestParameters ();
    preg_match ( $regExp, $url, $matches );
    $orderBy = "place";
    $direction = "asc";
    $sorted = false;
    if ( $matches && count ( $matches > 1 ) )
    {
        $sorted = true;
        list ( $orderBy, $direction ) = explode ( ".", $matches[1] );
    }
    $ereg = '/\\/orderBy\\/[^\\/]+/';
    foreach ( $columns as $key => $column )
    {
        if ( $key == $orderBy )
        {
            $replacement = '/orderBy/' . $key . "." . ( $direction == 'asc' ? 'desc' : 'asc' );
            $sortUrl = ( $sorted ? preg_replace ( $ereg, $replacement, $url ) : rtrim ( $url, "/" ) . $replacement ) . $query;
            $iTag = $direction == 'desc' ? "<i class='fa fa-sort-desc'></i>" : "<i class='fa fa-sort-asc'></i>";
        }
        else
        {
            $replacement = '/orderBy/' . $key . ".asc";
            $sortUrl = ( $sorted ? preg_replace ( $ereg, $replacement, $url ) : rtrim ( $url, "/" ) . $replacement ) . $query;
            $iTag = "";//$direction == "<i class='fa fa-long-arrow-up'></i>";
        }
        ?>
        <th data-name="<?php echo $key; ?>" title="<?php echo $column['attr']['title']; ?>"><a href="<?php echo $sortUrl; ?>"><?php echo $column['colTitle'] . "&nbsp;" . $iTag; ?></a></th>
        <?php
    }
    ?>
    </tr>
    <?php
    foreach ($result as $row)
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
            echo $row->$key;
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