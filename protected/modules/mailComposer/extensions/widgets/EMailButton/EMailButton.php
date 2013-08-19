<?php

/**
 * Одна большая ссылка, которая выглядит как кнопка.
 * Для какого-то важного действия, например "Подать заявку"
 * 
 * @todo дописать отображение пояснения для кнопки
 */
class EMailButton extends CWidget
{
    /**
     * @var string - тип кнопки (влияет только на цвет)
     *             success - зеленая
     *             main - синяя
     *             error - красная
     */
    public $type = 'success';
    /**
     * @var string - надпись на кнопке
     */
    public $caption;
    /**
     * @var string - ссылка, которая открывается при клике на кнопку
     */
    public $link;
    /**
     * @var string - описание под кнопкой
     */
    public $description;
    /**
     * @var string - цвет кнопки (определяется автоматически)
     */
    protected $color;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        switch ( $this->type )
        {
            case 'success': $this->color = '#7AB800'; break;
            case 'main':    $this->color = '#008AB8'; break;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->link OR ! $this->caption )
        {
            return;
        }
        $this->render('button');
    }
}