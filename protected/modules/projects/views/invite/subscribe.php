<?php
/**
 * Страница подписки по одноразовому ключу
 * 
 * @deprecated раньше использовалась для групп мероприятий, больше не нужна, удалить при рефакторинге
 */
/* @var $this   InviteController */
/* @var $invite EventInvite */

// запрет индексации поисковиками
Yii::app()->clientScript->registerMetaTag('noindex', 'robots');

//$this->breadcrumbs = array();

if ( $this->createMemberRequest($invite->questionary, $invite->event) )
{
    Yii::app()->getModule('user')->forceLogin($invite->questionary->user);
    $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
        'type'    => 'info',
        'header'  => 'Ваша заявка принята',
        'message' => 'На почту должно придти подтверждение регистрации.
            Если заявка будет одобрена - мы сообщим вам об этом.',
    ));
    
    /*echo $this->getInfoMessage('Ваша заявка принята. На почту должно придти подтверждение регистрации.
        Если заявка будет одобрена - мы сообщим вам об этом.',
    '', 'alert alert-block alert-info');*/
    // @todo ссылка на заявки
    /*$requestsUrl = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl,
    array(
        'id'        => $invite->questionary->id,
        'activeTab' => 'invites',)
    );
    echo CHtml::link('Просмотреть заявку', $projectUrl, array('class' => 'btn btn-success btn-large'));*/
}