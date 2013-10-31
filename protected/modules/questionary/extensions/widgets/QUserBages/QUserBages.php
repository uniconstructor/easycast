<?php

/**
 * Виджет выводящий список достижений и умений участника: спортсмен, актер, атлет, и т. д.
 */
class QUserBages extends CWidget
{
    /**
     * @var array - список достижений участника (берется из объекта анкеты)
     */
    public $bages;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        foreach ( $this->bages as $bage )
        {
            $this->widget('bootstrap.widgets.TbBadge', array(
                            'type'  => 'info',
                            'label' => $bage,
            ));
            echo ' ';
        }
    }
}