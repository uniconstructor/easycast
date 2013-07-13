<?php
/**
 * Виджет для отображения списка фотографий вместе с маленькой иконкой предпросмотра
 */
class EThumbCarousel extends CWidget
{
    /**
     * @var array - список маленьких картинок
     */
    public $previews;
    /**
     * @var array - список средних фотографий в галерее
     */
    public $photos;
    /**
     * @var array - список больших фотографий 
     */
    public $largePhotos;
    /**
     * @var string - html-id of the element
     */
    public $id = 'ethumbcarousel';
    /**
     * @var bool - вывести скрипты вместе с галереей (используется при AJAX-запросах)
     */
    public $echoScripts = false;
    
    protected $_assetUrl;
    
    protected $_scripts;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->_assetUrl = Yii::app()->assetManager->publish(
                        Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
                        'ECMarkup' . DIRECTORY_SEPARATOR .
                        'EThumbCarousel' . DIRECTORY_SEPARATOR .
                        'assets'   . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/EThumbCarousel.css');
        Yii::app()->clientScript->registerScriptFile($this->_assetUrl.'/EThumbCarousel.js');
        
        $previews = array();
        foreach ( $this->previews as $id=>$preview )
        {// создаем скрипты, которые меняют большую картинку при клике на маленькую
            $setPhotoScriptId = '_'.$this->id.'ChangePhoto#'.$preview['num'];
            //$setPhotoScript   = 'jQuery("#'.$this->id.'-thumbnail-'.$preview['num'].'").click(function (e) {e.preventDefault();jQuery("#'.$this->id.'").carousel('.$preview['num'].');});';
            $setPhotoScript   = 'jQuery("body").delegate("#'.$this->id.'-thumbnail-'.$preview['num'].'", "click", 
                function (e) {
                    e.preventDefault();
                    jQuery("#'.$this->id.'").carousel('.$preview['num'].');
            });';
            if ( $this->echoScripts )
            {
                $this->_scripts .= $setPhotoScript."\n";
            }else
          {
               Yii::app()->clientScript->registerScript($setPhotoScriptId, $setPhotoScript, CClientScript::POS_END);
            }
            $preview['baseId'] = $this->id;
            $previews[$id] = $preview;
        }
        $this->previews = $previews;
        
        $photos = array();
        foreach ( $this->photos as $id=>$photo )
        {
            $this->photos[$id]['imageOptions'] = array('id' => $this->id.'-photo-'.$photo['id']);
        }
    }
    
    /**
     * Отображает виджет: большой виджет просмотра фотографий и список маленьких иконок внизу 
     */
    public function run()
    {
        // Просмотр фотографий
        if ( $this->photos )
        {
            $this->widget('bootstrap.widgets.TbCarousel', array(
                            'items' => $this->photos,
                            'options' => array(
                                            'interval' => false,
                            ),
                            'htmlOptions' => array(
                                            'id' => $this->id,
                            ),
            ));
        }
        // Уменьшеные копии
        // @todo сделать так чтобы при включенной разбивки по страницам работал JS
        $dataProvider = new CArrayDataProvider($this->previews, array(
            'pagination'=>array(
                'pageSize'=>count($this->previews),
                )
            )
        );
        $this->widget('bootstrap.widgets.TbThumbnails', array(
                        'dataProvider' => $dataProvider,
                        'template'     => "{items}",
                        'itemView'     => '_thumb',
                        )
        );
        // fancybox для каждой фотографии, чтобы можно было посмотреть увеличенную копию
        foreach ( $this->largePhotos as $id=>$photo )
        {
            $this->widget('application.extensions.fancybox.EFancyBox', array(
                            'target'=>'#'.$this->id.'-photo-'.$photo['id'],
                            'config'=>array(
                                'href'   => $photo['image'],
                                'margin' => '0',
                                'hideOnContentClick' => true,
                                'showCloseButton' => false,
                            ),
                ));
        }
        if ( $this->echoScripts )
        {
            echo '<script>$(document).ajaxComplete(function(){'.$this->_scripts.';});</script>';
        }
    }
}