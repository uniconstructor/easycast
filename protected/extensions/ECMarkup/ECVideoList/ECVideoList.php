<?php

/**
 * Виджет для отображения списка видео: в анкете пользователя, проектах, мероприятиях,
 * и онлайн-кастинге
 * 
 * @package easycast
 * @subpackage questionary
 * 
 * @todo документировать оставшиеся функции
 * @todo добавить проверку обязательных параметров
 */
class ECVideoList extends CWidget
{
    /**
     * @var Video[] - список роликов для отображения
     */
    public $videos;
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var string
     */
    public $objectId;
    /**
     * @var int
     */
    public $limit = 12;
    /**
     * @var CDbCriteria
     */
    public $criteria;
    /**
     * @var bool
     */
    public $isAjaxRequest;
    
    /**
     * @var array - настройки для плагина shadowbox
     * @see http://www.shadowbox-js.com/options.html
     */
    public $shadowBox = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $defaults = $this->getShadowBoxDefaults();
        $this->shadowBox = CMap::mergeArray($defaults, $this->shadowBox);
        
        if ( $this->isAjaxRequest === null )
        {
            $this->isAjaxRequest = Yii::app()->request->isAjaxRequest;
        }
        
        $this->setCriteria();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // @todo пока непонятно как встраивать видео из vk - не показываем его в списке, чтобы не 
        //       давать ссылку на конкретных людей
        $this->criteria->addCondition("`type` != 'vkontakte'");
        
        $dataProvider = new CActiveDataProvider('Video',  array(
            'criteria'   => $this->criteria,
            'pagination' => array('pageSize' => $this->limit),
        ));
        
        echo CHtml::openTag('div', array('class' => 'row-fluid'));
        $this->widget('bootstrap.widgets.TbThumbnails', array(
                'dataProvider' => $dataProvider,
                'template' => "{items}\n{pager}",
                'itemView' => 'ext.ECMarkup.ECVideoList.views.video',
            )
        );
        echo CHtml::closeTag('div');
    }
    
    /**
     * Настройки по умолчанию для плагина shadowbox
     * @return array
     */
    protected function getShadowBoxDefaults()
    {
        return array(
            'autoplayMovies' => false,
            //'width' => '360px',
        );
    }
    
    /**
     * 
     * @return void
     */
    protected function setCriteria()
    {
        if ( $this->criteria )
        {// условия поиска уже заданы, не меняем их
            return;
        }
        
        $this->criteria = new CDbCriteria();
        //$this->criteria->limit = $this->limit;
        $this->criteria->compare('objecttype', $this->objectType);
        $this->criteria->compare('objectid', $this->objectId);
    }
}