<?php

/**
 * Виджет, составляющий описание для заказа (обычного и срочного), заявки на проведение онлайн-кастинга
 * или заявки на расчет стоимости
 */
class OrderDescription extends CWidget
{
    /**
     * @var FastOrder - созданный заказ, по которому составляется описание
     */
    public $order;
    /**
     * @var string - кому составляется письмо
     *               team     - оповещение команде
     *               customer - подтверждение заказчику
     */
    public $target;
    /**
     * @var string - где будет использовано полученное описание заказа?
     *               email    - для составления письма
     *               megaplan - для создания задачи в Мегаплане
     */
    public $type = 'email';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.models.*');
        if ( ! ( $this->order instanceof FastOrder ) )
        {
            throw new CException('Не передан заказ для составления описания');
        }
        if ( ! $this->target OR ! in_array($this->target, array('team', 'customer')) )
        {
            throw new CException('Не указано для кого составлять описание заказа');
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        switch ( $this->order->type )
        {// текст описания выбирается в зависимости от типа заказа
            case FastOrder::TYPE_FAST:
                $this->render($this->target.'/fastOrder', null);
            break;
            case FastOrder::TYPE_NORMAL:
                $this->render($this->target.'/order', null);
            break;
            case FastOrder::TYPE_CASTING:
                $this->render($this->target.'/casting', null);
            break;
            case FastOrder::TYPE_CALCULATION:
                $this->createCalculationDescription();
            break;
            default: throw new CException('Неизвестный тип заказа'); break;
        }
    }
    
    /**
     * Получить описание заявки на расчет стоимости (для заказчика или участника)
     * @return string
     */
    protected function createCalculationDescription()
    {
        $orderData = $this->order->loadOrderData();
        if ( $orderData['eventtime'] == 'day' )
        {
            $eventTime = 'Дневная съемка';
        }else
        {
            $eventTime = 'Ночная съемка';
        }
        // не отображаем пункты описания заявки если они не указаны
        $comment = '';
        if ( isset($orderData['comment']) AND $orderData['comment'] )
        {
            $comment = 'Дополнительные коментарии к заявке: '.$orderData['comment'];
        }
        $planDate = '';
        if ( isset($orderData['plandate']) AND $orderData['plandate'] )
        {
            $planDate = '<li>Планируемая дата съемок: '.$orderData['plandate'].'</li>';
        }
        $daysNum = '';
        if ( isset($orderData['daysnum']) AND $orderData['daysnum'] )
        {
            $daysNum = '<li>Количество дней: '.$orderData['daysnum'].'</li>';
        }
        $duration = '';
        if ( isset($orderData['duration']) AND $orderData['duration'] )
        {
            $daysNum = '<li>Длительность смены: '.$orderData['duration'].' ч.</li>';
        }
    
        $params = array(
            'orderData' => $orderData,
            'eventTime' => $eventTime,
            'planDate'  => $planDate,
            'daysNum'   => $daysNum,
            'duration'  => $duration,
            'comment'   => $comment,
        );
    
        $this->render($this->target.'/calculation', $params);
    }
    
    /**
     * Создать описание для заявки на онлайн-кастинг
     * @return string
     */
    protected function createCastingDescription()
    {
        
    }
}