<?php

/**
 * Виджет подвала для темы Maximal
 */
class ECResponsiveFooter extends CWidget
{
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('footer');
        if ( ! Yii::app()->user->checkAccess('Admin') AND ! YII_DEBUG )
        {// Выводим счетчик Яндекса
            $this->render('yandexCounter');
        }
        // выводим скрипт онлайн-консультанта
        if ( ! Yii::app()->user->checkAccess('Admin') AND ! YII_DEBUG )
        {// (для всех кроме админов)
            $this->render('zopim');
        }
    }
}