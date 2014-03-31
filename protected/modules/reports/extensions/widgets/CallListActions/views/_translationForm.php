<?php
/**
 * Страница с полями формы, которая показывается только в том случае когда фотовызывной нужно перевести 
 * на английский язык
 */
/* @var $this CallListActions */
/* @var $form TbActiveForm */

echo CHtml::label('Перевод названия проекта ['.CHtml::encode($this->event->project->name).']', false);
echo CHtml::textField('translation[project]');

echo CHtml::label('Перевод названия события ['.CHtml::encode($this->event->name).']', false);
echo CHtml::textField('translation[event]');
echo '<hr>';

foreach ( $this->event->vacancies as $vacancy )
{// создаем форму перевода для каждой роли мероприятия
    echo CHtml::label('Перевод названия роли "<b>'.CHtml::encode($vacancy->name).'</b>"', false);
    echo CHtml::textField('translation[vacancy]['.$vacancy->id.']');
}
echo '<br>';