<?php

/**
 * Фильтр для поиска по полю email (для администраторов)
 * @todo добавить подсказку при вводе
 */
class QSearchFilterEmail extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('email');
    
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
        return "email";
    }
    
    /**
     * получить все html-содержимое виджета фрагмента формы поиска
     * (функция заменяет run() для обычных виджетов)
     *
     * @return null
     */
    protected function getContent()
    {
        $content = '';
        $name    = $this->getFullInputName('email');
        
        // Получаем данные по умолчанию
        $default = '';
        if ( $params = $this->loadLastSearchParams() )
        {
            $default = $params['email'];
        }
        $htmlOptions = array('placeholder' => 'email');
        // Выводим текстовое поле для ФИО
        $content .= CHtml::textField($name, $default, $htmlOptions);
        
        return $content;
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии или динамически обновлять содержимое поиска)
     * (этот метод - индивидуальный для каждого фильтра)
     * 
     * @return string
     */
    protected function createCollectFilterDataJs()
    {
        $selector = $this->inputSelectors['email'];
        
        return  "function {$this->collectDataJsName}(){
            var data  = {};
            var value = jQuery.trim(jQuery('{$selector}').val());
            
            if ( value.length > 0 )
            {
                data.email = value;
            }
            return data;
        };";
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     *
     * @return string
     */
    protected function createClearFormDataJs()
    {
        $selector = $this->inputSelectors['email'];
        return "$('{$selector}').val('')";
    }
}