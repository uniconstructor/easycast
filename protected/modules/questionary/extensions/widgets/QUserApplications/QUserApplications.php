<?php

/**
 * Виджет, отображающий все заявки участника
 * 
 * @todo сделать вывод остальных типов заявок
 * @todo сделать переключение между типами заявок
 * @todo группировать заявки по мероприятиям и проектам
 * @todo языковые строки
 */
class QUserApplications extends CWidget
{
    /**
     * @var Questionary - анкета для которой отображаются приглашения
     */
    public $questionary;
    
    /**
     * @var string - какие заявки выводить изначально
     *               new   - новые (поданые + предварительно отобранные)
     *               draft - только поданые (ждут решения администратора или режиссера)
     *               pending - предварительно отобраны
     *               rejected - отклонены
     *               active - одобренные (то есть предстоящие съемки)
     */
    public $mode = 'new';
    
    /**
     * @var bool - отображать заголовок "мои заявки"?
     */
    public $displayHeader = true;
    
    /**
     * @var array - отображаемые заявки (объекты или наследники класса ProjectMember)
     */
    protected $items;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! ($this->questionary instanceof Questionary) )
        {
            throw new CException(500, 'В виджет "'.get_class($this).'" не передана анкета.');
        }
        $this->initItems();
    }
    
    /**
     * Получить и запомнить список отображаемых элементов (заявок)
     * @return array
     * 
     * @todo сделать отображение списка в зависимости от типа - сейчас всегда отображаем только "new"
     */
    protected function initItems()
    {
        switch ( $this->mode )
        {
            case 'draft': break;
            case 'pending': break;
            case 'rejected': break;
            case 'new': 
                $statuses = array(
                    ProjectMember::STATUS_DRAFT,
                    ProjectMember::STATUS_INCOMPLETE,
                    ProjectMember::STATUS_FINALIZED,
                    ProjectMember::STATUS_PENDING,
                );
                $this->items = ProjectMember::model()->forQuestionary($this->questionary->id)->
                    withStatus($statuses)->findAll(); 
            break;
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->displayHeader )
        {
            echo '<h3>'.$this->getHeader().'</h3>';
        }
        if ( ! $this->items )
        {// нет ни одной заявки 
            $this->displayEmptyMessage();
            return;
        }
        foreach ( $this->items as $item )
        {// отображаем все заявки участника
            $options = $this->getItemDisplayOptions($item);
            $this->render('application', $options);
        }
    }
    
    /**
     * Получить заголовок виджета
     * @return string
     */
    protected function getHeader()
    {
        return 'Мои заявки';
    }
    
    /**
     * Получить параметры для отображения одной заявки через render()
     * @param MemberRequest $item - отображаемая заявка
     * @return array
     */
    protected function getItemDisplayOptions($item)
    {
        // получаем доступные кнопки для заявки
        $actions = $this->widget('projects.extensions.MemberActions.MemberActions',
            array('member' => $item), true);
        // создаем ссылку на мероприятие
        $eventUrl = Yii::app()->createUrl('/projects/projects/view', array('eventid' => $item->vacancy->event->id));
        
        $options = array();
        $options['item']        = $item;
        $options['vacancy']     = $item->vacancy;
        $options['eventUrl']    = $eventUrl;
        // получаем лого проекта
        $options['projectLogo'] = $item->vacancy->event->project->getAvatarUrl('small');
        $options['actions']     = $actions;

        return $options;
    }
    
    /**
     * Отобразить сообщение о том, что заявок нет
     * @param string $mode - какие заявки пытаемся вывести
     * @return string
     * 
     * @todo выводить разные сообщения в зависимости от режима отображения
     */
    protected function displayEmptyMessage($mode=null)
    {
        $text = 'Пусто';
        $this->render('message', array('text' => $text));
    }
}