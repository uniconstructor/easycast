<?php

/**
 * Виджет для вывода списка новостей
 * 
 * @todo прикрутить orbitSlider
 * @todo запоминать страницу в сессию при навигации
 */
class NewsList extends CWidget
{
    /**
     * @var CActiveDataProvider - список всех новостей, которые нужно отобразить,
     *                            источник данных для TbListView при отображении
     */
    protected $dataProvider;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('news.models.News');
        
        $criteria = new CDbCriteria();
        $criteria->addCondition('`visible` = 1');
        $criteria->order = '`timecreated` DESC';
        
        $this->dataProvider = new CActiveDataProvider('News',
            array(
                'criteria'   => $criteria,
                'pagination' => array('pageSize' => $this->getNewsDisplayCount()),
            )
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->widget('bootstrap.widgets.TbListView', array(
            'dataProvider' => $this->dataProvider,
            'template'     => "{pager}{items}{pager}",
            'itemView'     => '_newsThumb',
            //'afterAjaxUpdate'=>$this->getPagerAfterAjaxUpdate(),
        ));
    }
    
    /**
     * Получить количество новосей, отображаемых на одной странице
     * @return number
     * 
     * @todo брать параметр из настроек
     */
    protected function getNewsDisplayCount()
    {
        return 20;
    }
}