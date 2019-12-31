<form action='/admin/?module=Sport&method=<?php echo $function; ?>' method='post' enctype="multipart/form-data">
    <?php
    if ( $mode !== "prop" )
    {


        $query = "SELECT
            `t`.`id` AS id,
            CONCAT(`s`.`title`,' / ',`t`.`title`) AS title,
            IF(NOW()>`t`.`startDate` AND NOW()<`t`.`endDate`,1,0) AS selected
            FROM `prefix_tourneys` `t`
            JOIN `prefix_seasons` `s` ON `s`.`id`=`t`.`seasonId`";
        $tourneys = SqlTools::selectObjects ( $query );
        ?>
        Выберите турнир<br>
        <select name="tourney">
        <?php
        foreach ( $tourneys as $tourney )
        {
            ?><option value="<?php echo $tourney->id; ?>" <?php echo ( $tourney->selected ? "selected" : "" ); ?>><?php echo $tourney->title; ?></option><?php
        }
    }
    ?>
    </select><br>
    Файл с данными<br>
    <input type='file' name='<?php echo $mode; ?>'><br>
    <input type='hidden' name='mode' value='<?php echo $mode; ?>'>
    <input type='submit' value='Загрузить'>
</form>
