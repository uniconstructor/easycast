<?php

/**
 * Фильтр поиска по статусу
 * @todo всегда оставлять в поле select2 хотя бы 1 статус
 */
class QSearchFilterStatus extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('status');
    
    /**
     * @see QSearchFilterBase::enabled()
     */
    public function enabled()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
    /**
     * @see QSearchFilterBase::getTitle()
    */
    protected function getTitle()
    {
        return "Статус";
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        return array(
            Questionary::STATUS_DRAFT => QuestionaryModule::t('status_'.Questionary::STATUS_DRAFT),
            Questionary::STATUS_ACTIVE => QuestionaryModule::t('status_'.Questionary::STATUS_ACTIVE),
            Questionary::STATUS_PENDING => QuestionaryModule::t('status_'.Questionary::STATUS_PENDING),
            Questionary::STATUS_DELAYED => QuestionaryModule::t('status_'.Questionary::STATUS_DELAYED),
            Questionary::STATUS_REJECTED => QuestionaryModule::t('status_'.Questionary::STATUS_REJECTED),
        );
    }
    
    /**
     * @see QSearchFilterBaseSelect2::createSelectVariants()
     */
    protected function createSelectVariants()
    {
        $options  = array();
        $selected = array();
    
        // Получаем варианты для выпадающего меню
        $variants = $this->getMenuVariants();
    
        if ( $params = $this->loadLastSearchParams() AND isset($params[$this->getShortName()]) )
        {// получаем значения по умолчанию (если они были)
            $selected = $params[$this->getShortName()];
        }else
        {// по статусу не может не быть фильтра - поэтому если значение не установлено - запишем хоть
            // какие-то значения
            $selected = array(Questionary::STATUS_ACTIVE, Questionary::STATUS_PENDING, Questionary::STATUS_REJECTED);
        }
    
        // создаем массив пунктов для выпадающего меню, и устанавливаем значения по умолчанию
        foreach ( $variants as $value=>$label )
        {
            $option = array();
            $option['label'] = $label;
            if ( in_array($value, $selected) )
            {
                $option['selected'] = 'selected';
            }
            $options[$value] = $option;
        }
        return $options;
    }
}