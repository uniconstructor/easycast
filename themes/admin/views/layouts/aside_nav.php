<?php
/**
 * Разметка боковой навигации
 */
/* @var $this Controller */
?>
<aside id="left-panel">
    <!-- User info -->
    <div class="login-info">
        <span><!-- User image size is adjusted inside CSS, it should stay as it --> 
            <a href="javascript:void(0);" id="show-shortcut">
                <img alt="me" class="online" src="img/avatars/sunny.png">
                <span>john.doe</span>
            </a>
        </span>
    </div>
    <!-- end user info -->
    <nav>
        <ul>
            <li class=""><a href="ajax/dashboard.html" title="Dashboard"><span class="menu-item-parent">Dashboard</span></a></li>
            <li>...</li>
            <li><a href="#"><span class="menu-item-parent">Graphs</span></a>
                <ul>
                    <li><a href="ajax/flot.html">...</a></li>
                    <li>...</li>
                    <li>...</li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>