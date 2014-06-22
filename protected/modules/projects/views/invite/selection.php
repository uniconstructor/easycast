<?php
/**
 * Страница отбора актеров для заказчика
 * @todo создать layout с выделеным местом под affix
 */
/* @var $customerInvite CustomerInvite */
/* @var $this InviteController */

//$this->breadcrumbs = array();

// блок всплывающих сообщений
$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="container">
    <div class="row">
        <div class="span12">
            <div class="title-page">
                <h1 class="title"><?= $customerInvite->vacancy->name; ?></h1>
            </div>
        </div>
    </div>
    <?php
    if ( $customerInvite->objecttype != 'vacancy' )
    {// отбор заявок на мероприятие или проект - пока используем старый виджет
        $this->widget('application.modules.projects.extensions.TokenSelection.TokenSelection', array(
            'displayType'    => 'applications',
            'customerInvite' => $customerInvite,
        ));
    }
    ?>
</div>
<div class="page page-alternate" style="z-index:200;">
    <?php 
    if ( $customerInvite->objecttype === 'vacancy' )
    {// отбор заявок на одну роль - используем новый виджет
        $this->widget('application.modules.admin.extensions.wizards.processor.MemberProcessor.MemberProcessor', array(
            'customerInvite'    => $customerInvite,
            'sectionInstanceId' => Yii::app()->request->getParam('siid', 0),
            'currentMemberId'   => Yii::app()->request->getParam('cmid', 0),
            'lastMemberId'      => Yii::app()->request->getParam('lmid', 0),
            'draft'             => Yii::app()->request->getParam('draft', 0),
            'pending'           => Yii::app()->request->getParam('pending', 0),
            'active'            => Yii::app()->request->getParam('active', 0),
            'rejected'          => Yii::app()->request->getParam('rejected', 0),
            'nograde'           => Yii::app()->request->getParam('nograde', 0),
            'good'              => Yii::app()->request->getParam('good', 0),
            'normal'            => Yii::app()->request->getParam('normal', 0),
            'sad'               => Yii::app()->request->getParam('sad', 0),
        ));
    }
    ?>
</div>