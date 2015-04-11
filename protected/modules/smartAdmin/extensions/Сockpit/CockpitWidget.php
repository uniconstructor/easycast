<?php

/**
 * 
 */
class CockpitWidget extends CWidget
{
    /**
     * @see parent::init()
     */
    public function run()
    {
        $this->render('cockpit');
    }
}