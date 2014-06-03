<?php

/**
 * Виджет для редактирования списка доп. полей в роли
 * Позволяет прикреплять к роли дополнительные поля, которые могут быть обязательными при заполнении
 * при подаче заявки
 * 
 * @todo расширить функциюнал этого виджета, добавив возможность использовать его для событий и ролей
 */
class ExtraFieldsManager extends CWidget
{
    /**
     * @var EventVacancy
     */
    public $vacancy;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('manager');
    }
}