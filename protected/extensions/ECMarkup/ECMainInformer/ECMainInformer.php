<?php
/**
 * Виджет, отображающий главную информацию для пользователя:
 * Для гостей - "стать участником" (стать заказчиком)
 * Для тех у кого корзина - количество товаров
 * Для участников - новые приглашения и сообщения
 */
class ECMainInformer extends CWidget
{
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('main');
    }
}