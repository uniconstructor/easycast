<?php

/**
 * 
 * @todo документировать класс
 * @todo выводить аватар проекта
 */
class QUserNotifications extends CWidget
{
    /**
     * @var Questionary
     */
    public $questionary;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $message        = '';
        $memberStatuses = array(
            ProjectMember::STATUS_ACTIVE,
            ProjectMember::STATUS_DRAFT,
            ProjectMember::STATUS_INCOMPLETE,
            ProjectMember::STATUS_PENDING,
        );
        $applications = ProjectMember::model()->forQuestionary($this->questionary)->
            withStatus($memberStatuses)->findAll();
        if ( ! $applications OR ! $this->questionary )
        {
            return '';
        }
        
        foreach ( $applications as $application )
        {/* @var $application ProjectMember */
            if ( $application->event->isExpired() )
            {
                continue;
            }
            if ( $application->vacancy->needMoreDataFromUser($this->questionary) )
            {
                $formUrl = Yii::app()->createAbsoluteUrl('/projects/vacancy/registration', array(
                    'vid' => $application->vacancy->id,
                ));
                $formLink = CHtml::link('Дополнить заявку', $formUrl, array(
                    'class' => 'btn btn-primary btn-large',
                ));
                $message  = '<div class="alert alert-danger">';
                $message .= '<div class="row-fluid">';
                $message .= '<h4>Пожалуйста укажите дополнительные данные в вашей заявке</h4>';
                $message .= 'Для дальнейшего рассмотрения вашей заявки мы просим вас указать 
                    дополнительную информацию.  Нажмите на кнопку "Дополнить заявку" чтобы
                    сделать это сейчас <br> '.$formLink;
                $message .= '</div>';
                $message .= '</div>';
            }
        }
        echo $message;
    }
}