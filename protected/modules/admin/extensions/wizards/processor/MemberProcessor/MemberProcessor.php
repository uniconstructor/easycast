<?php

/**
 * Виджет для обработки списка заявок и распределение их по разделам внутри роли
 * Используется администраторами или заказчиками при отборе заявок 
 */
class MemberProcessor extends CWidget
{
    /**
     * @var EventVacancy - роль для которой происходит отбор участников
     */
    public $vacancy;
    /**
     * @var CustomerInvite
     */
    public $customerInvite;
    /**
     * @var int - id текущей просматриваемой вкладки (CatalogSectionInstance)
     *            0 -  неразобранные заявки
     */
    public $sectionInstanceId = 0;
    /**
     * @var int - id текущей просматриваемой заявки (ProjectMember)
     *            Всегда 0 если указан sectionId (в разделах видны только списки)
     *            Если и sectionId и currentMemberId по нулям - то currentMemberId вычисляется
     */
    public $currentMemberId   = 0;
    /**
     * @var int - последняя проверенная анкета: если 0 - то нет ссылки назад
     */
    public $lastMemberId      = 0;
    // статусы
    /**
     * @var int
     */
    public $draft    = 1;
    /**
     * @var int
     */
    public $pending  = 1;
    /**
     * @var int
     */
    public $active   = 1;
    /**
     * @var int
     */
    public $rejected = 0;
    // маркеры
    /**
     * @var int
     */
    public $nograde  = 1;
    /**
     * @var int
     */
    public $good     = 1;
    /**
     * @var int
     */
    public $normal   = 1;
    /**
     * @var int
     */
    public $sad      = 0;
    /**
     * @var string - страница на которой расположен этот виджет
     */
    public $widgetRoute = '/projects/invite/selection/';
    /**
     * @var string - параметры для таблицы со списком возможных разделов
     */
    public $sectionGridOptions = array(
        'gridControllerPath' => '/admin/memberInstanceGrid/',
        'updateUrl'          => '/projects/invite/editMemberInstance',
    );
    
    /**
     * @var CatalogSection
     */
    protected $section;
    /**
     * @var CatalogSectionInstance
     */
    protected $sectionInstance;
    /**
     * @var array
     */
    protected $markers  = array('sad', 'nograde', 'normal', 'good');
    /**
     * @var array
     */
    protected $statuses = array('active', 'pending', 'draft', 'rejected');
    /**
     * @var string название текущего раздела
     */
    protected $sectionName = 'Без категории';
    /**
     * @var unknown
     */
    protected $memberCount = array('0' => '');
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->customerInvite )
        {
            $this->vacancy = $this->customerInvite->vacancy;
        }
        if ( ! $this->vacancy )
        {
            throw new CException('Не найдена роль для отбора заявок');
        }
        
        if ( $this->sectionInstanceId )
        {// просматриваем раздел с заявками - получим по нему всю информацию
            $this->sectionInstance = CatalogSectionInstance::model()->findByPk($this->sectionInstanceId);
            $this->section         = $this->sectionInstance->section;
            $this->sectionName     = $this->section->name;
        }
        $this->memberCount['0'] = $this->vacancy->countUnallocatedMembers($this->currentStatuses);
        foreach ( $this->vacancy->catalogSectionInstances as $instance )
        {
            $this->memberCount[$instance->id] = '';
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('main');
    }
    
    /**
     * Получить параметры для сохранения состояния виджета
     * @param array $params
     * @return array
     */
    protected function getLinkOptions($params=array())
    {
        $options = array(
            'vid'      => $this->vacancy->id,
            'siid'     => $this->sectionInstanceId,
            'cmid'     => $this->currentMemberId,
            'lmid'     => $this->lastMemberId,
            'draft'    => $this->draft,
            'pending'  => $this->pending,
            'active'   => $this->active,
            'rejected' => $this->rejected,
            'nograde'  => $this->nograde,
            'good'     => $this->good,
            'normal'   => $this->normal,
            'sad'      => $this->sad,
        );
        if ( $this->customerInvite )
        {
            $options['id'] = $this->customerInvite->id;
            $options['k1'] = $this->customerInvite->key;
            $options['k2'] = $this->customerInvite->key2;
        }
        return CMap::mergeArray($options, $params);
    }
    
    /**
     * Получить url, который возвращает пользователя обратно к виджету
     * 
     * @param array $params
     * @return string
     */
    protected function getReturnUrl($params=array())
    {
        return Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions($params));
    }
    
    /**
     * Получить $url на изменение одного bool параметра 
     * @param string $name - название параметра
     * @return string
     */
    protected function getToggleUrl($name)
    {
        return $this->getReturnUrl(array($name => intval( ! $this->$name)));
    }
    
    /**
     * Получить код кнопки предназначеной для изменения одного bool параметра
     * (определяет нажата ли изначально кнопка, раскрашивает ее, формирует ссылку)
     * @param string $name - название параметра
     * @return string
     */
    protected function getToggleButton($name)
    {
        $url         = $this->getToggleUrl($name);
        $label       = '';
        $activeClass = '';
        $htmlOptions = array(
            'class' => 'btn ',
            'style' => 'margin-top:0px;width:25%;min-height:75px;',
        );
        switch ( $name )
        {
            case 'draft': 
                $label       = 'Ждут решения';
                $activeClass = 'btn-info';
            break;
            case 'pending':
                $label       = '<small>Предвари-тельно отобраны</small>';
                $activeClass = 'btn-warning';
            break;
            case 'active':
                $label       = 'Утверждены';
                //$label       .= '<i style="font-size:24px;" class="icon icon-check"></i>';
                $activeClass = 'btn-success';
            break;
            case 'rejected': 
                $label       = 'Отклонены';
                $activeClass = 'btn-danger';
            break;
            case 'nograde':
                $label       = 'Без оценки';
                $activeClass = 'btn-info';
            break;
            case 'good':
                $label       = 'Лучшие';
                $activeClass = 'btn-success';
            break;
            case 'normal':
                $label       = 'Средние';
                $activeClass = 'btn-warning';
            break;
            case 'sad':
                $label       = 'Худшие';
                $activeClass = 'btn-danger';
            break;
        }
        if ( $this->$name )
        {
            $htmlOptions['class'] .= $activeClass.' active';
        }
        
        return CHtml::link($label, $url, $htmlOptions);
    }
    
    /**
     * 
     * @return void
     */
    protected function getCurrentStatuses()
    {
        $result = array();
        foreach ( $this->statuses as $status )
        {
            if ( $this->$status )
            {
                $result[] = $status;
            }
        }
        return $result;
    }
    
    /**
     * 
     * @return void
     */
    protected function getCurrentMarkers()
    {
        $result = array();
        foreach ( $this->markers as $marker )
        {
            if ( $this->$marker )
            {
                $result[] = $marker;
            }
        }
        return $result;
    }
    
    /**
     * Получить заявку участника для редактирования в виджете обработки заявок
     * 
     * @return ProjectMember|null
     * 
     * @todo выставлять блокировку перед началом редактирования и снимать ее после
     */
    protected function getCurrentMember()
    {
        if ( ! $member = ProjectMember::model()->findByPk($this->currentMemberId) )
        {// если текущая заявка не задана - берем любую не занятую
            $member = $this->vacancy->getUnallocatedMember($this->currentStatuses);
        }
        return $member;
    }
    
    /**
     * 
     * @param int $sectionId
     * @return int
     */
    protected function getMemberCount($sectionInstanceId=0)
    {
        return $this->memberCount[$sectionInstanceId];
    }
}