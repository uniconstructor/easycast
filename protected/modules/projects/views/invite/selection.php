<?php
/**
 * Страница отбора актеров для заказчика
 */
/* @var $customerInvite CustomerInvite */

// убираем из заголовка все лишнее
// @todo убрать все обращения к ecHeaderOptions - этот параметр в классе Controller больше не используется
/*$this->ecHeaderOptions = array(
    'displayloginTool' => false,
    'displayInformer'  => false,
);*/

$this->breadcrumbs = array();

// блок всплывающих сообщений
$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Отбор актеров</h1>
                </div>
            </div>
        </div>
        <?php 
        // список всех заявок
        // @todo сделать возможность отправки уникальной ссылки на список анкет в любом наборе статусов
        $this->widget('application.modules.projects.extensions.TokenSelection.TokenSelection', array(
            //'objectType'     => $customerInvite->objecttype,
            //'objectId'       => $customerInvite->objectid,
            'displayType'    => 'applications',
            'customerInvite' => $customerInvite,
        ));
        ?>
    </div>
</div>