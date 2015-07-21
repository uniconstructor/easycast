<?php

/**
 * Виджет подвала для темы Maximal
 */
class ECResponsiveFooter extends CWidget
{
    /**
     * @var bool - включить/выключить инструменты и счетчики веб-аналитики на странице (Яндекс, Google)
     */
    public $analytics;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->analytics === null )
        {
            $this->analytics = $this->getController()->analytics;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('footer');
        if ( Yii::app()->user->isGuest AND $this->analytics AND ! YII_DEBUG )
        {// счетчик Яндекса
            $this->render('yandexCounter');
        }
        if ( Yii::app()->user->isGuest AND ! YII_DEBUG )
        {// скрипт онлайн-консультанта (для всех кроме админов)
            //$this->render('zopim');
        }
    }
    
    /**
     * 
     * @return void
     */
    protected function printFooterMenu()
    {
        $links = array();
        $mode  = Yii::app()->getModule('user')->getViewMode(false);
        
        if ( $mode === 'user' )
        {
            $items = $this->getUserMenu();
        }elseif ( $mode === 'customer' )
        {
            $items = $this->getCustomerMenu();
        }else
        {
            $items = $this->getUserMenu();
        }
        foreach ( $items as $item )
        {
            $url     = Yii::app()->createAbsoluteUrl($item['url']);
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
                'url'  => current(Yii::app()->getModule('user')->registrationUrl),
                'text' => 'Регистрация',
            );
            $items[] = array(
                'url'  => '//site/index',
                'text' => 'На страницу выбора',
            );
        }else
        {
            $items[] = array(
                'url'  => '//questionary/questionary/view',
                'text' => 'Моя страница',
            );
        }
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            $items[] = array(
                'url'  => '//search',
                'text' => 'Поиск',
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
                'url'  => '//search',
                'text' => 'Поиск',
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
            array(
                'url'  => '//calculation',
                'text' => 'Рассчитать стоимость съемки',
            ),
        );
        if ( Yii::app()->user->checkAccess('Admin') )
        {// упрощаем админам навигацию по сайту
            $items[] = array(
                'url'  => '//site/index',
                'text' => 'На страницу выбора',
            );
            $items[] = array(
                'url'  => '//admin',
                'text' => 'Администрирование',
            );
        }
        return $items;
    }
}