<?php

/**
 * Виджет для создания ркдактирования и заполнения списка 
 * 
 * @todo документировать поля и методы
 */
class EasyListManager extends CWidget
{
    /**
     * @var EasyList - 
     */
    public $easyList;
    /**
     * @var string - 
     */
    public $createUrl;
    /**
     * @var string -
     */
    public $updateUrl;
    /**
     * @var string -
     */
    public $deleteUrl;
    /**
     * @var string - 
     */
    public $createItemUrl;
    /**
     * @var string -
     */
    public $updateItemUrl;
    /**
     * @var string -
     */
    public $deleteItemUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! ($this->easyList instanceof EasyList) )
        {
            $this->easyList = new EasyList;
            $this->easyList->unique         = 1;
            $this->easyList->triggercleanup = EasyList::TRIGGER_NEVER;
            $this->easyList->triggerupdate  = EasyList::TRIGGER_NEVER;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('manager');
    }
}