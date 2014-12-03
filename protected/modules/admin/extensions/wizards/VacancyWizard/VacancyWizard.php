<?php

/**
 * Виджет для создания и редактирования роли в админке
 * Содержит в себе 3 шага: 
 * 1) внесение общей информации
 * 2) настройка фильтров поиска
 * 3) выбор обязательных и дополнительных полей 
 * 
 * @todo не заработал та как нужно
 */
class VacancyWizard extends CWidget
{
    /**
     * @var EventVacancy - редактируемая роль: может быть isNewRecord, но только на первом этапе
     */
    public $vacancy;
    /**
     * @var string
     */
    public $step = 'info';
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('wizard');
    }
    
    /**
     * Получить содержимое вкладки с формой ввода или редактирования основной информации
     * @return string
     */
    protected function getInfoTab()
    {
        return $this->render('_info', array(
            'model' => $this->vacancy,
        ), true);
    }
    
    /**
     * Получить содержимое вкладки с формой критериев поиска на роль
     * @return string
     */
    protected function getFiltersTab()
    {
        if ( ! $this->vacancy->isNewRecord )
        {// для еще не созданных записей мы не можем отобразить критерии поиска
            return $this->render('_filters', array(), true);
        }
    }
    
    /**
     * Получить содержимое вкладки с редактором обязательных и дополнительных полей
     * @return string
     */
    protected function getExtraFieldsTab()
    {
        if ( ! $this->vacancy->isNewRecord )
        {// для еще не созданных записей мы не можем отобразить дополнительные поля
            return $this->render('_extrafields', array(), true);
        }
    }
    
    /**
     * Проверить, является ли переданая вкладка активной
     * @param string $tab
     * @return bool
     */
    protected function isActiveTab($tab)
    {
        return ($tab === $this->step);
    }
}