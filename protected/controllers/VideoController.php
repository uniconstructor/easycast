<?php 

// Подключаем родительский класс контроллера сложных значений
Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для работы с видео: позволяет прикреплять видеоролики к любым моделям
 * 
 * @package easycast
 * 
 * @todo пока работает только с анкетой. Добавить возможность использовать этот контроллер 
 *       не только для видео в анкете но и для других объектов
 * @todo перенести добавить дополнительный уровень абстракции: класс BaseGridController
 *       и наследовать от него QComplexValueController
 * @todo добавить возможность добавлять видео незарегистрированным пользователям
 *      (только если это понадобится в форме быстрой регистрации)
 */
class VideoController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения формы: в этом контроллере значение всегда будет 'Video',
     *               само поле нужно для работы родительского класса
     * @see QComplexValueController::modelClass
     */
    protected $modelClass = 'Video';
    /**
     * @var sting - тип объекта которому по умолчанию принадлежит видео
     */
    protected $objectType;
    /**
     * @var int
     */
    protected $objectId;
    
    /**
     * Проверить, есть ли у пользователя доступ к добавлению, редактированию или удалению объекта
     * @param Video $item
     * @return void
     */
    protected function checkAccess($item)
    {
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            return true;
        }
        switch ( $item->objecttype )
        {
            case 'questionary': 
                if ( $item->objectid == Yii::app()->getModule('questionary')->getCurrentQuestionary() )
                {// в свою анкету участникам можо загружать видео
                    return true;
                }
                return false;
            break;
            case 'project': 
                return Yii::app()->user->checkAccess('Admin');
            break;
        }
        return true;
    }
}