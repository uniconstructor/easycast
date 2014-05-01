<?php
/**
 * Меню участника
 */
/* @var $this ECResponsiveMenu */
?>
<div class="row-fluid">
    <?php if ( Yii::app()->user->isGuest ) { ?>
    <div class="span3">            
        <a class="box" href="/easy">
            <div class="icon">
                <i class="icon-plus"></i>
            </div>
            <h4>Регистрация</h4>
            <!--p></p-->
        </a>
    </div>
    <?php } else { ?>
    <div class="span3">            
        <a class="box" href="/questionary/questionary/view">
            <div class="icon">
                <i class="icon-user"></i>
            </div>
            <h4>Моя страница</h4>
            <!--p></p-->
        </a>
    </div>
    <?php } ?>
    <div class="span3">            
        <a class="box" href="/agenda">
            <div class="icon">
                <i class="icon-tasks"></i>
            </div>
            <h4>События</h4>
            <!--p></p-->
        </a>
    </div>
    <div class="span3">            
        <a class="box" href="/calendar">
            <div class="icon">
                <i class="icon-calendar"></i>
            </div>
            <h4>Календарь</h4>
            <!--p></p-->
        </a>
    </div>
    <div class="span3">            
        <a class="box" href="/projects">
            <div class="icon">
                <i class="icon-film"></i>
            </div>
            <h4>Проекты</h4>
            <!--p></p-->
        </a>
    </div>
</div>