<?php
/**
 * Разметка боковой навигации
 * 
 * @todo вынести в отдельный виджет
 * 
 * To make this navigation dynamic please make sure to link the node
 * (the reference to the nav > ul) after page load. Or the navigation
 * will not initialize.
 */
/* @var $this        Controller */
/* @var $questionary Questionary */

$module = Yii::app()->getModule('questionary');
if ( Yii::app()->user->isGuest )
{
    $avatar = '<span class="online glyphicon glyphicon-eye-open"></span>';
    $name   = 'Временный доступ';
}else
{
    $avatar = Yii::app()->user->getAvatarImage('small', array('class' => 'online'));
    $name   = Yii::app()->user->fullName;
}
?>
<!-- #NAVIGATION -->
<!-- Left panel : Navigation area -->
<aside id="left-panel">
    <!-- User info -->
    <div class="login-info">
        <span><!-- User image size is adjusted inside CSS, it should stay as it --> 
            <a href="javascript:void(0);" id="show-shortcut">
                <?= $avatar; ?>
                <span><?= $name; ?></span>
            </a>
        </span>
    </div>
    <!-- end user info -->
    <nav>
        <?php 
        // левая колонка: главное меню с навигацией
        $this->widget('smartAdmin.extensions.SideBar');
        ?>
    </nav>
</aside>
<!-- END NAVIGATION -->
<!-- jGrowl messages shoud be displayed below ribbon -->
<div id="jGrowl" class="jGrowl"></div>