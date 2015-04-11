<?php

/**
 * 
 */
class CockpitAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        $this->controller->renderPartial('cockpit');
    }
}
