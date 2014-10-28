<?php

/**
 * Админский виджет для перемещения заявки участника между ролями
 * 
 * @todo добавить возможность копировать заявку из одной роли в другую
 */
class ChangeMemberVacancy extends CWidget
{
    /**
     * @var array
     */
    public $vacancyStatuses = array();
    /**
     * @var ProjectMember
     */
    public $member;
    /**
     * @var string
     */
    public $actionUrl = 'admin/projectMember/changeVacancy';
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $vacancies = $this->getAvailableVacancies() AND (count($vacancies) > 1) )
        {
            echo '<div class="well well-small">';
            echo '<b>'.$this->member->getAttributeLabel('vacancyid').':</b>&nbsp;';
            $this->widget('bootstrap.widgets.TbEditableField', array(
                    'type'      => 'select',
                    'model'     => $this->member,
                    'attribute' => 'vacancyid',
                    'url'       => Yii::app()->createUrl($this->actionUrl),
                    'source'    => $vacancies,
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                )
            );
            echo '</div>';
        }
    }
    
    /**
     * 
     * 
     * @return array
     */
    protected function getAvailableVacancies()
    {
        $vacancies = EventVacancy::model()->forEvent($this->member->event->id)->
            withStatus($this->vacancyStatuses)->findAll();
        $options = CHtml::listData($vacancies, 'id', 'name');
        
        return ECPurifier::getEditableSelectOptions($options);
    }
}