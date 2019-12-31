<div id="header-right">
    <div class="user-profile dropdown">
        <a href="#" title="" class="user-ico clearfix" data-toggle="dropdown">
            <span>Личный кабинет: <?php echo $user['name']; ?></span>
            <i class="glyph-icon icon-chevron-down"></i>
        </a>
        <div class="dropdown-menu pad0B float-right">
            <div class="box-sm">
                <div class="login-box clearfix">
                    <div class="user-info">
                        <span>
                            <?php echo $user['name']; ?> (<?php echo $login; ?>)
                            <?php echo empty($user['post']) ? '' : '<i>' . $user['post'] . '</i>'; ?>
                            <?php echo empty($user['email']) ? '' : '<i>' . $user['email'] . '</i>'; ?>
                        </span>
                    </div>
                </div>
                <div class="divider"></div>
                <ul class="reset-ul mrg5B">
                    <li>
                        <a href="/admin/?module=System">
                            Системные настройки и утилиты
                        </a>
                    </li>
                </ul>
                <div class="pad5A button-pane button-pane-alt text-center">
                    <a href="?logout" class="btn display-block font-normal btn-danger">
                        <i class="glyph-icon icon-power-off"></i>
                        Выйти
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div id="page-nav-right">
        <a href="/" class="btn" target="_blank">
            Перейти на сайт
            <i class="glyph-icon icon-external-link"></i>
        </a>
    </div>
</div>