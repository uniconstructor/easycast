<?php
/**
 * Страница с сообщением о том, что ни одного события сейчас не идет
 */
/* @var $this EventsAgenda */

$extraText = '';
if ( ! Yii::app()->user->isGuest AND ! Yii::app()->user->checkAccess('Customer') AND $this->userMode == 'user' )
{
    $extraText = 'Если для вас найдутся подходящие роли - мы сразу же вам сообщим.';
}
?>
<div class="alert alert-info alert-block">
    <h4 class="alert-heading">Предстоящих событий нет</h4>
    <p>Новые съемки скоро появятся - скорее всего сейчас мы готовим к запуску еще один проект.
    Мы опубликуем его как только соберем всю необходимую информацию.
    <?= $extraText; ?></p>
</div>