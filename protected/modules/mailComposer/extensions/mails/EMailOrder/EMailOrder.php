<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Письмо для заказчика или команды, которое отправляется после создания заказа
 * Используется:
 * - при оформлении обычного заказа
 * - при оформлении срочного заказа
 * - при создании онлайн-кастинга
 * - при создании заявки на расчет стоимости
 */
class EMailOrder extends EMailBase
{
    /**
     * @var FastOrder - созданный заказ, для которого отправляется это письмо
     */
    public $order;
    /**
     * @var string - кому составляется письмо
     *               - team     оповещение команде
     *               - customer подтверждение заказчику
     */
    public $target;
    
    /**
     * @see EMailBase::init()
     */
    public function init()
    {
        parent::init();
        
        if ( ! ( $this->order instanceof FastOrder ) )
        {
            throw new CException('Не передан заказ для составления письма');
        }
        if ( ! $this->target OR ! in_array($this->target, array('team', 'customer')) )
        {
            throw new CException('Не указано для кого составлять письмо');
        }
    }
    
    /**
     * @see EMailBase::run()
     */
    public function run()
    {
        switch ( $this->target )
        {
            case 'customer': $mainText = $this->createTextForCustomer(); break;
            case 'team':     $mainText = $this->createTextForTeam(); break;
        }
        // добавляем блок с полным текстом письма
        $this->addSegment($mainText);
        // создаем письмо на стандартном бланке, с заголовком, шапкой и подвалом
        parent::run();
    }
    
    /**
     * 
     * @return array - массив с блоком текста для составления письма 
     */
    protected function createTextForTeam()
    {
        switch ( $this->order->type )
        {// текст письма выбирается в зависимости от типа заказа
            case FastOrder::TYPE_FAST:
                $header = 'Оформлен срочный заказ';
                $text   = $this->render('team/fastOrder', null, true);
            break;
            case FastOrder::TYPE_NORMAL:
                $header = 'Новый заказ на сайте';
                $text = $this->render('team/order', null, true);
            break;
            case FastOrder::TYPE_CASTING:
                $header = 'Новая заявка на проведение онлайн-кастинга';
                $text = $this->render('team/casting', null, true);
            break;
            case FastOrder::TYPE_CALCULATION:
                $header = 'Новая заявка на расчет стоимости';
                $text = $this->render('team/calculation', null, true);
            break;
            default: throw new CException('Неизвестный тип заказа'); break;
        }
        
        return $this->textBlock($text, $header);
    }
    
    /**
     * 
     * @return array - массив с блоком текста для составления письма
     */
    protected function createTextForCustomer()
    {
        switch ( $this->order->type )
        {// текст письма выбирается в зависимости от типа заказа
            case FastOrder::TYPE_FAST:
                $header = 'Ваш заказ принят';
                $text   = $this->render('customer/fastOrder', null, true);
            break;
            case FastOrder::TYPE_NORMAL:
                $header = 'Ваш заказ принят';
                $text = $this->render('customer/order', null, true);
            break;
            case FastOrder::TYPE_CASTING:
                $header = 'Заявка на проведение онлайн-кастинга принята';
                $text = $this->render('customer/casting', null, true);
            break;
            case FastOrder::TYPE_CALCULATION:
                $header = 'Заявка на расчет стоимости принята';
                $text = $this->render('customer/calculation', null, true);
            break;
            default: throw new CException('Неизвестный тип заказа'); break;
        }
        
        return $this->textBlock($text, $header);
    }
}