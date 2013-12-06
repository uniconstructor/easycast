<?php

// подключаем базовый класс
Yii::import('application.modules.mailComposer.extensions.mails.EMailSSNotification.EMailSSNotification');

/**
 * Письмо, которое отправляется пользователям при ручной регистрации их в нашей базе
 */
class EMailECRegistration extends EMailSSNotification
{
    /**
     * Получить текст с описанием рекомендации
     * @return string
     */
    protected function createRecommendationInfo()
    {
        $projectsLink = CHtml::link('наших проектов', 'http://easycast.ru/projects');
        return 'Некоторое время назад вы проходили у нас кастинг (возможно вы помните на по прошлому названию "b-glance"), 
            и мы решили снова связаться с вами для того чтобы предложить вам принять участие в съемках '.$projectsLink.'.';
    }
}