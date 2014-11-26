<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 * 
 * @todo убрать sideBar, header и subtitle если их использование будет 
 *       ограничиваться только темой оформления SmartAdmin 
 */
class Controller extends RController
{
    /**
     * @var array - левая панель навигации в меню
     */
    public $sideBar = array();
    /**
     * @var string
     */
    public $pageHeader;
    /**
     * @var string
     */
    public $subTitle;
    /**
     * @var bool - включить/выключить инструменты и счетчики веб-аналитики на странице (Яндекс, Google)
     */
    public $analytics = true;
     
    /**
     * @see CController::filters()
     * 
     * @todo совместить с полной заменой accessFilter на RBAC если будет возможность
     */
    public function filters()
    {
        return array(
            array(
                // фильтр обработки ссылок с токенами
                'application.filters.ECReferalFilter',
            ),
            // @todo фильтр, который заставляет использовать только защищенное соединение 
            /*array(
                'ext.sweekit.filters.SwProtocolFilter - parse',
                'mode' => 'https',
            ),*/
        );
    }
    
    /**
     * @see parent::behaviors()
     * 
     * @return array
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        // Подключаем ко всем контроллерам проекта методы для вывода js-кода: 
        // redirectJs(), renderJs(), renderJson()
        $behaviors = array(
            'sweelixRendering' => array(
                'class' => 'ext.sweekit.behaviors.SwRenderBehavior',
            ),
        );
        return CMap::mergeArray($parentBehaviors, $behaviors);
    }
}