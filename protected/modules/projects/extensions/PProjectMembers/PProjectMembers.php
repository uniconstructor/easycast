<?php

/**
 * Виджет для отображения списка участников проекта или заявок на участие в проекте
 */
class PProjectMembers extends CWidget
{
    /**
     * @var int id проекта, мероприятия или вакансии - 
     *           в зависимости от того что отображается в текущий момент
     */
    public $objectid;
    
    /**
     * Тип объекта для которого отображаются участники
     * @var string project|event|vacancy
     */
    public $objecttype;
    
    /**
     * Тип отображения: все участники, заявки
     * @var string applications|members
     */
    public $displaytype;
    
    public function run()
    {
        
    }
    
    protected function getProjectSection()
    {
        
    }
    
    protected function getEventSection()
    {
        
    }
    
    protected function getVacancySection()
    {
        
    }
    
    protected function getVacancyMembers($vacancyid)
    {
        
    }
}