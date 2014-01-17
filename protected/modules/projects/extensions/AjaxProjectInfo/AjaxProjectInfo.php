<?php

/**
 * Отображение информации по проекту по AJAX-запросу
 * Отличается от обычной информации по проекту тем, что может передает JS-скрипты вместе с виджетом,
 * чтобы они могли правильно работать, когда информация загружается через AJAX
 * 
 * @todo добавить разворачивающийся список доступных ролей
 * @todo добавить проверку входных значений
 * @todo сделать добавление tooltip-подсказки через скрипты (иначе не сработает)
 */
class AjaxProjectInfo extends CWidget
{
    /**
     * @var Project - проект по которому отображается информация
     */
    public $project;
    /**
     * @var string - режим просмотра сайта: участник или заказчик
     */
    public $userMode;
    /**
     * @var bool - отображать ли блок с краткой информацией о проекте?
     */
    public $displayShortInfoBlock = false;
    /**
     * @var bool - отображать ли логотип проекта?
     */
    public $displayLogo = true;
    /**
     * @var bool - отображать ли краткое описание под логотипом?
     */
    public $displayShortDescription = true;
    
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        if ( ! in_array($this->userMode, array('user', 'customer')) )
        {
            $this->userMode = Yii::app()->getModule('user')->getViewMode();
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $description = $this->project->getFullDescription($this->userMode);
        if ( $this->displayShortInfoBlock )
        {
            $this->render('fullData', array(
                'logo' => $this->getProjectLogo(),
                'description' => $description,
            ));
        }else
        {
            $this->render('onlyDescription', array(
                'description' => $description,
            ));
        }
    }
    
    /**
     * Получить html-код фрагмента с логотипом и названием проекта
     * @return string
     */
    protected function getProjectLogo()
    {
        if ( ! $this->displayLogo )
        {
            return '';
        }
        $logoUrl      = $this->event->project->getAvatarUrl('small', true);
        $projectUrl   = Yii::app()->createUrl('/projects/projects/view', array(
            'id' => $this->project->id,
        ));
    
        // логотип проекта
        $image = CHtml::image($logoUrl, '', array(
            'class' => 'ec-event-info-logo img-polaroid media-object',
        ));
        // настройки логотипа: при клике на него должно открываться окно с полной информацией о проекте
        $imageLinkOptions = array(
            'target'         => '_blank',
            'data-toggle'    => 'tooltip',
            'data-title'     => 'Перейти на страницу проекта<br>(в новом окне)',
            'data-html'      => true,
            'data-placement' => 'bottom',
        );
        $image = CHtml::link($image, $projectUrl, $imageLinkOptions);
    
        if ( $this->displayProjectName )
        {// отображаем название проекта под логотипом (если нужно)
            // параметры для ссылки на проект: всегда открывается в новом окне, добавляется подсказка
            $projectName = '<small class="muted">'.$project->name.'</small>';
        }
    
        return $image.$projectName;
    }
}