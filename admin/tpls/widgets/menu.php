<div id="sidebar-menu">
    <ul>
        <li>
            <a href="/admin">
                <i class="glyph-icon icon-linecons-tv"></i>
                <span>Панель управления</span>
            </a>
        </li>
        <?php foreach($modules as $m) { ?>
            <li <?php if($_GET['module'] == $m['module']) echo 'class="active"'; ?>>
                <a href="?module=<?php echo $m['module'] ?>">
                    <?php
                    if($m['icon'])
                        $class = "glyph-icon icon-". $m['icon'];
                    else if ( $m['classIcon'] )
                        $class = $m['classIcon'];
                    else
                        $class = '';
                    ?><i class="<?php echo $class; ?>"></i>&nbsp;<span><?php echo $m['name'] ?></span>
                </a>
            </li>
            <?php } ?>
    </ul>
</div>