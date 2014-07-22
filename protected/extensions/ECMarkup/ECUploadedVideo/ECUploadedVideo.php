<?php

/**
 * Список загруженных на сайт видео, воспроизведение происходит через html5.
 * Позже видео будет заменено на потоковое, как только станет ясно как настраивать Amazon CloudFront
 */
class ECUploadedVideo extends CWidget
{
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var int
     */
    public $objectId;
    /**
     * @var int|string - время после которого все ссылки на видео превращаются в тыкву
     */
    public $expires;
    
    /**
     * @var array
     */
    protected $videos;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('objecttype', $this->objectType);
        $criteria->compare('objectid', $this->objectId);
        $criteria->compare('type', 'file');
        
        $this->videos = Video::model()->findAll($criteria);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->videos )
        {
            return;
        }
        echo CHtml::openTag('ul');
        foreach ( $this->videos as $video )
        {/* @var $video Video */
            $url = Sweeml::raiseOpenShadowboxUrl('#', array(
                'player'  => 'html',
                'content' => '<div><video controls src="'.$video->getEmbedUrl($this->expires).'" width="640" height="480"></video></div>',
                'width'   => 640,
                'height'  => 480,
            ));
            echo CHtml::tag('li', array(), CHtml::link(CHtml::encode($video->name), $url));
        }
        echo CHtml::closeTag('ul');
    }
}