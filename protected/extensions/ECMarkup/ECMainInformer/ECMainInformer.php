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
        if ( Yii::app()->user->isGuest )
        {// гостю предлагаем стать участником
            $this->render('main');
        }elseif ( $this->isCustomer() )
        {// заказчикам показываем корзину
            $this->render('customer');
        }elseif ( $this->isUser() )
        {// участникам показываем сообщения
            $this->render('user');
        }
    }
    
    /**
     * Определить, является ли пользователь заказчиком (он вошел как заказчик
     * или добавил хотя бы 1 анкету в заказ)
     * 
     * @return bool
     */
    protected function isCustomer()
    {
        $orders = FastOrder::getPendingOrderUsers();
        if ( ! empty($orders) )
        {// у пользователя есть хотя бы 1 заказ - это заказчик
            return true;
        }
        return Yii::app()->user->checkAccess('Customer');
    }
    
    /**
     * определить, является ли пользователь участником
     * 
     * @return bool
     */
    protected function isUser()
    {
        return ( Yii::app()->user->checkAccess('User') AND ! Yii::app()->user->checkAccess('Admin') );
    }
}