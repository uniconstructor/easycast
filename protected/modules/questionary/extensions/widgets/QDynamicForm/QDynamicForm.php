<?php

/**
 * Виджет динамической формы анкеты участника, составляемый по частям
 * В форме могут быть простые и "сложные" поля
 * Простое поле - это одно скалярное значение, которое хранится в таблице questionary
 *                для него чаще всего нужно вывести один элемент: например текстовое поле или галочку, и т. д.
 * Сложное поле - это, как правило, набор связанных с анкетой объектов, которых может быть
 *                произвольное количество: например список фильмов, в которых снимался актер
 *                учебные заведения, которые он окончил, и т. д.
 *                Для таких полей выводится или виджет select2 или виджет ввода сложных значений,
 *                Все виджеты ввода сложных значений наследуются от класса QGridEditBase
 * 
 * Поля группируются по разделам.
 * Основная информация, внешность, условия, умения и навыки, настройки, дополнительно
 */
class QDynamicForm extends CWidget
{
    /**
     * @var array - поля формы, которые должны быть отображены
     *              представляет собой массив строк
     *              каждая строка это или название поля в таблице questionary
     *              или название связи (relation) модели Questionary
     */
    public $fields;
    /**
     * @var string - режим отображения: 
     *               registration - форма регистрации на мероприятие
     *               full         - полная форма анкеты
     *               popup        - всплывающая форма (в modal-окне)
     *               test         - тест "пройду ли я кастинг"
     */
    public $viewMode   = 'registration';
    /**
     * @var Questionary - создаваемая или редактируемая анкета
     */
    public $questionary;
    /**
     * @var bool - разбивать ли форму на разделы
     */
    public $sections = true;
    /**
     * @var string - тип объекта для которого составляется форма:
     *               questionary - id загружнной анкеты (для формы редактирования)
     *               event       - id события, заявки на которое
     *               project
     *               vacancy
     */
    //public $objectType = 'event';
    /**
     * @var int - id объекта
     */
    //public $objectId   = 0;
        
    /**
     * @var ProjectEvent 
     */
    //public $event;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->assembleClasses();
        switch ( $this->objectType )
        {
            case 'questionary': $this->assembleClasses(); break;
        }
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        
    }
    
    /**
     * 
     * @return string - полный код формы
     */
    protected function getForm()
    {
        
    }
}