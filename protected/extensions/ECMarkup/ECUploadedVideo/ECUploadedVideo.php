<?php

/**
 * Список загруженных на сайт видео, воспроизведение происходит через html5.
 * Позже видео будет заменено на потоковое, как только станет ясно как настраивать Amazon CloudFront
 * 
 * @todo изначально извлекать только оцифрованные видео
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
        /*$encodedFiles  = Video::model()->withType('file')->encodedOnly()->
                forObject($this->objectType, $this->objectId)->findAll();
        $originalFiles = Video::model()->withType('file')->originalOnly()->
                forObject($this->objectType, $this->objectId)->findAll();*/
        /*if ( $encodedFiles )
        {
            $this->videos = $encodedFiles;
        }else
        {
            $this->videos = $originalFiles;
        }*/
        $videos = Video::model()->withType('file')->
                forObject($this->objectType, $this->objectId)->findAll();
        foreach ( $videos as $video )
        {
            $this->videos[$video->id] = $video;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->videos )
        {// когда нет видео - показываем заказчикам сообщение об этом 
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'Видео пока не загружено',
            ));
            return;
        }
        // отображаем список видео
        echo CHtml::openTag('ul', array('class' => 'text-left'));
        foreach ( $this->videos as $video )
        {/* @var $video Video */
            $htmlOptions = array('id' => 'video'.$video->id);
            if ( ! $video->externalFile->originalid AND ! Yii::app()->user->checkAccess('Admin') )
            {
                continue;
            }elseif ( ! $video->externalFile->originalid AND Yii::app()->user->checkAccess('Admin') )
            {
                $video->name .= ' [оригинал]';
                $htmlOptions  = array('class' => 'muted');
            }
            $url = Sweeml::raiseOpenShadowboxUrl('#', array(
                'player'  => 'html',
                'content' => '<div><video controls src="'.$video->getEmbedUrl($this->expires).'" width="640" height="480"></video></div>',
                'width'   => 650,
                'height'  => 490,
            ));
            echo CHtml::tag('li', $htmlOptions, CHtml::link(CHtml::encode($video->name), $url, $htmlOptions));
        }
        echo CHtml::closeTag('ul');
    }
}