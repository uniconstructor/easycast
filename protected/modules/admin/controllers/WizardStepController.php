<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для работы со списком шагов регистрации
 * 
 * @deprecated не используется, удалить при рефакторинге
 */
class WizardStepController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'WizardStep';
    
    /**
     * @see EditableGridController::actionCreate()
     */
    public function actionCreate()
    {
        $step = parent::actionCreate();
        
        // чаще всего при создании нового шага регистрации мы сразу же привязываем его к роли
        $objectType = Yii::app()->request->getParam('objectType');
        $objectId   = Yii::app()->request->getParam('objectId', 0);
        if ( $objectType AND $objectId )
        {
            $instance = new WizardStepInstance();
            $instance->objecttype   = $objectType;
            $instance->objectid     = $objectId;
            $instance->wizardstepid = $step->id;
            $instance->save();
        }
    }
    
    /**
     * Проверить, есть ли у пользователя доступ к добавлению, редактированию или удалению объекта
     * @param CActiveRecord $item
     * @return void
     */
    protected function checkAccess($item)
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {
            throw new CHttpException(400, 'Ошибка при изменении записи');
        }
        return true;
    }
}