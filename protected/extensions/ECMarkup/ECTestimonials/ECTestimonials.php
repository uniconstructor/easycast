<?php

/**
 * Отзывы от заказчиков
 * @todo добавить возможность просмотре через слайдер
 */
class ECTestimonials extends CWidget
{
    /**
     * @var string - режим просмотра: все отзывы друг за другом (blocks)
     *               или слайдер с возможностью листать (slider)
     */
    public $mode = 'blocks';
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('blocks');
    }
}