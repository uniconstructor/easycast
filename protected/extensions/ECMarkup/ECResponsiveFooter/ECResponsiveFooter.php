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
    
    /**
     * 
     * @return void
     */
    protected function printFooterMenu()
    {
        $mode = Yii::app()->getModule('user')->getViewMode();
        if ( $mode === 'user' )
        {
            $items = $this->getUserMenu();
        }else
        {
            $items = $this->getCustomerMenu();
        }
        $links = array();
        foreach ( $items as $item )
        {
            $url  = Yii::app()->createAbsoluteUrl($item['url']);
            //$text = mb_strtoupper($item['text']);
            $links[] = CHtml::link($item['text'], $url).' ';
        }
        
        echo implode(' | ', $links);
    }
    
    /**
     * 
     * @return array
     */
    protected function getUserMenu()
    {
        $items = array(
            array(
                'url'  => '//site/index',
                'text' => 'На главную',
            ),
            array(
                'url'  => '//calendar',
                'text' => 'Календарь',
            ),
            array(
                'url'  => '//projects',
                'text' => 'Наши проекты',
            ),
            array(
                'url'  => '//agenda',
                'text' => 'Наши события',
            ),
        );
        if ( Yii::app()->user->isGuest )
        {
            $items[] = array(
                'url'  => Yii::app()->user->registrationUrl,
                'text' => 'Регистрация',
            );
        }else
        {
            $items[] = array(
                'url'  => '//questionary/questionary/view',
                'text' => 'Моя страница',
            );
        }
        return $items;
    }
    
    /**
     *
     * @return array
     */
    protected function getCustomerMenu()
    {
        $items = array(
            array(
                'url'  => '//site/index',
                'text' => 'На главную',
            ),
            array(
                'url'  => '//catalog',
                'text' => 'Все актеры',
            ),
            array(
                'url'  => '//projects',
                'text' => 'Наши проекты',
            ),
            array(
                'url'  => '//agenda',
                'text' => 'Наши события',
            ),
            array(
                'url'  => '//sale',
                'text' => 'Коммерческое предложение',
            ),
            /*array(
                'url'  => '//site/index',
                'text' => 'На страницу выбора',
            ),*/
        );
        return $items;
    }
}