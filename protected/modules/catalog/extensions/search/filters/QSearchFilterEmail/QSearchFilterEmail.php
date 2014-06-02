<?php

/**
 * Фильтр для поиска по полю email (для администраторов)
 * @todo добавить подсказку при вводе
 */
class QSearchFilterEmail extends QSearchFilterBaseSelect2
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
     * Получить настройки для плагина select2
     *
     * @return array
     */
    protected function createSelect2Options()
    {
        $selected = array();
        if ( $params = $this->loadLastSearchParams() AND isset($params[$this->getShortName()]) )
        {// получаем значения по умолчанию (если они были)
            $selected = $params[$this->getShortName()];
        }
        return array(
            // текст-заглушка
            'placeholder'    => '',
            // разрешить удалять верианты из списка
            'allowClear'     => true,
            // select не закрывается, чтобы можно было быстро выбрать несколько вариантов
            'closeOnSelect'  => false,
            // отсылать событие change каждый раз при изменении данных
            'triggerChange ' => true,
            // максимальная ширина всегда
            'width'          => '100%',
            // разрешаем ввод собственных значений (пока не добавлен autocomplete при вводе)
            // @todo параметр tags не разрешен в select2 когда используется select
            //       вернуться к использованию getMenuVariants
            //'tags'           => $selected,
        );
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        return array();
    }
    
    /**
     * @see QSearchFilterBaseSelect2::createSelectVariants()
     */
    protected function createSelectVariants()
    {
        return array();
    }
}