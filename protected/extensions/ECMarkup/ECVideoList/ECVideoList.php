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
    public $limit = 6;
    /**
     * @var CDbCriteria
     */
    public $criteria;
    
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
        parent::init();
        
        $defaults = $this->getShadowBoxDefaults();
        $this->shadowBox = CMap::mergeArray($defaults, $this->shadowBox);
        
        $this->setCriteria();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
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
        {
            return;
        }
        
        $this->criteria = new CDbCriteria();
        //$this->criteria->limit = $this->limit;
        $this->criteria->compare('objecttype', $this->objectType);
        $this->criteria->compare('objectid', $this->objectId);
    }
}