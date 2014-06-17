<?php

/**
 * Виджет, отображающий все дополнительные поля прикрепленные к анкете при подаче заявки на роль
 */
class ExtraFieldsList extends CWidget
{
    /**
     * @var ProjectMember
     */
    public $member;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $data       = array('id' => 1);
        $attributes = array();
        if ( ! $this->member->vacancy->extraFields )
        {
            return;
        }
        foreach ( $this->member->vacancy->extraFields as $extraField )
        {/* @var $extraField ExtraField */
            $value = ExtraFieldValue::model()->forField($extraField->id)->
                forVacancy($this->member->vacancy->id)->forQuestionary($this->member->memberid)->find();
            if ( $value AND $extraField->type != 'checkbox' )
            {
                $data[$extraField->name] = $value->value;
                $attributes[] = array(
                    'name'  => $extraField->name,
                    'label' => $extraField->label,
                );
                $data[$extraField->name] = $value->value;
            }
        }
        
        $this->widget('bootstrap.widgets.TbDetailView', array(
                'data'       => $data,
                'attributes' => $attributes,
            )
        );
    }
}