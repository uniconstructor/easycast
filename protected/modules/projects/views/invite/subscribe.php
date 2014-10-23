<?php
/**
 * Страница подписки по одноразовому ключу
 */
/* @var $this InviteController */

//$this->breadcrumbs = array();

if ( $this->createMemberRequest($invite->questionary, $invite->event) )
{
    $this->quickLogin($invite->questionary);
    echo $this->getInfoMessage('Сейчас вам на почту должно придти подтверждение.
                Как только заявка будет одобрена - мы сообщим вам об этом по почте, а также пришлем
                более подробную информацию об участии.',
    'Ваша заявка принята', 'alert alert-block alert-info');
    // @todo ссылка на заявки
    /*$requestsUrl = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl,
    array(
        'id'        => $invite->questionary->id,
        'activeTab' => 'invites',)
    );
    echo CHtml::link('Просмотреть заявку', $projectUrl, array('class' => 'btn btn-success btn-large'));*/
}