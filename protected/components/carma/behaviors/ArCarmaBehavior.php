<?php

/**
 * Стандартное поведение для всех моделей CustomActiveRecord
 * Отвечает за отправку событий при каждом изменении модели
 */
class ArCarmaBehavior extends CActiveRecordBehavior
{
    /**
	 * Responds to {@link CActiveRecord::onBeforeSave} event.
	 * Override this method and make it public if you want to handle the corresponding
	 * event of the {@link CBehavior::owner owner}.
	 * You may set {@link CModelEvent::isValid} to be false to quit the saving process.
	 * @param CModelEvent $event event parameter
	 */
	protected function beforeSave($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onAfterSave} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	protected function afterSave($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onBeforeDelete} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * You may set {@link CModelEvent::isValid} to be false to quit the deletion process.
	 * @param CEvent $event event parameter
	 */
	protected function beforeDelete($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onAfterDelete} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	protected function afterDelete($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onBeforeFind} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	protected function beforeFind($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onAfterFind} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	protected function afterFind($event)
	{
        
	}

	/**
	 * Responds to {@link CActiveRecord::onBeforeCount} event.
	 * Override this method and make it public if you want to handle the corresponding event
	 * of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 * @since 1.1.14
	 */
	protected function beforeCount($event)
	{
        
	}
}