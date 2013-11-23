<?php
/**
 * Список актеров (для заказчика) или список проектов (для участника)
 */
/* @var $this ETopRated */
?>
</div>
</div>

<div class="ec-wrapper">
<div id="ec-content">
    <?php 
    if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Admin') )
    {
        echo '<div class="our_rezim"></div>';
    }
    ?>
    <div class="slider_fon"></div>
    <div class="our_faces"></div>
    <ul class="nav nav-tabs" style="margin-bottom: 15px;">
        <li class="active"><a href="#current" data-toggle="tab">Все</a></li>
    </ul>
    <div id="myTabContent" class="tab-content" style="overflow:visible;">
    <?php
       // отображаем выборку актеров по одному параметру
       $this->render('_list', array());
    ?>
    </div>
</div>
</div>

<div class="wrapper">
<div id="content">