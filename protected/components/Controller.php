<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends RController
{
    /**
     * @var array левая панель навигации в меню
     */
    public $sideBar = array();
     
    /**
     * 
     * @todo совместить с полной заменой accessFilter на RBAC если будет возможность
     * @see CController::filters()
     */
    public function filters()
    {
        return array(
            array(
                'application.filters.ECReferalFilter',
            ),
            // @todo (запланировано) фильтр, который заставляет пользователей использовать только защищенное соединение
            /*array(
                'ext.sweekit.filters.SwProtocolFilter - parse',
                'mode' => 'https',
            ),*/
        );
    }
    
    /**
     * @see parent::behaviors()
     * @return array
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        // Подключаем ко всем контроллерам проекта методы для вывода js-кода: redirectJs(), renderJs(), renderJson()
        $behaviors = array(
            'sweelixRendering' => array(
                'class' => 'ext.sweekit.behaviors.SwRenderBehavior',
            ),
        );
        return CMap::mergeArray($parentBehaviors, $behaviors);
    }
}