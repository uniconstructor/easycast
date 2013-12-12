<?php

/**
 * Наш API для работы с мегапланом
 */
class EasyCastMegaplanAPI extends CApplicationComponent
{
    /**
     * @see CApplicationComponent::init()
     */
    public function init()
    {
        Yii::import('application.components.megaplan.*');
        parent::init();
    }
}