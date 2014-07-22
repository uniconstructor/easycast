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
     * @var string - адрес по которому происходит обработка AJAX-редактирования дополнительных полей
     */
    public $editUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->editUrl )
        {
            $this->editUrl = Yii::app()->createUrl('/admin/extraFieldValue/update');
        }
        parent::init();
    }
    
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
                $attribute = array(
                    'name'  => $extraField->name,
                    'label' => $extraField->label,
                );
                if ( Yii::app()->user->checkAccess('Admin') )
                {// админам разрешаем редактировать ответы участников
                    $data[$extraField->name] = $this->widget('bootstrap.widgets.TbEditableField', array(
                        'type'      => 'textarea',
                        'model'     => $value,
                        'attribute' => 'value',
                        'url'       => $this->editUrl,
                        'params'    => array(
                            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                        ),
                        // отображаем форму редактирования внутри ячейки чтобы не глючило наложение
                        'mode'    => 'inline',
                        'options' => array(
                            'onblur' => 'submit',
                        ),
                    ), true);
                    // разрешаем в столбце ответа html для того чтобы сработал 
                    // админский элемент редактирования ответов
                    $attribute['type'] = 'raw';
                }else
                {// всем остальным только смотреть
                    $data[$extraField->name] = $value->value;
                }
                
                $attributes[] = $attribute;
                unset($attribute);
            }
        }
        
        $this->widget('bootstrap.widgets.TbDetailView', array(
                'data'       => $data,
                'attributes' => $attributes,
            )
        );
    }
}