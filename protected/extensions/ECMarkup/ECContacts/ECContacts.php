<?php
/**
 * Небольшой виджет, выводящий список контактов
 * @todo выводить разные телефоны в зависимости от того кто просматривает страницу (участник или заказчик)
 */
class ECContacts extends CWidget
{
    /**
     * @var array - список тех контактов, которые нужно вывести
     *              (phone|email|feedback|likes) 
     */
    public $displayItems = array('phone', 'email', 'feedback');
    /**
     * @var array - список контактов на главной странице, в верхнем блоке
     */
    protected $_contacts = array();
    
    /**
     * @var string
     */
    protected $userPhone;
    
    /**
     * @var string
     */
    protected $customerPhone;
    
    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        // загружаем список контактной информации
        $this->_contacts = $this->initContacts();
    }
    
    /**
     * Отображает таблицу с контактами
     */
    public function run()
    {
        $this->render('contacts');
    }

    /**
     * Получить список контактных данных для левого блока страницы
     * @return array
     */
    protected function initContacts()
    {
        $this->userPhone     = Yii::app()->params['userPhone'];
        $this->customerPhone = Yii::app()->params['customerPhone'];
        
        /*$contacts = array();

        // телефон
        if ( in_array('phone', $this->displayItems) )
        {
            $contacts['phone'] = array(
                'icon'   =>  'icon-phone icon-white',
                'label'  => '+7&nbsp;(906)&nbsp;098&nbsp;32&nbsp;07',
                'itemOptions' => array('style' => ' margin-top: 0px;', 'id' => 'ec_contact_phone'),
            );
        }
        
        // email
        if ( in_array('email', $this->displayItems) )
        {
            $contacts['email'] = array(
                'icon'  =>  'icon-envelope icon-white',
                'label'  => 'mail@easycast.ru',
                'itemOptions' => array('style' => ' margin-top: 0px;'),
            );
        }
        
        // Обратная связь
        if ( in_array('feedback', $this->displayItems) )
        {
            $contacts['feedback'] = array(
                'icon' =>  'icon-comment icon-white',
                'label'  => Yii::t('coreMessages','send_feedback'),
                'itemOptions' => array('style' => ' margin-top: 0px;'),
                'url' => '/site/contact',
            );
        }

        return $contacts;*/
    }
}
