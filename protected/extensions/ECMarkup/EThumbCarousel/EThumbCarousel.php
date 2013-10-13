<?php
/**
 * Виджет для отображения списка фотографий вместе с маленькой иконкой предпросмотра
 * @todo убрать вариант с echoScripts, заменить его на создание виджета через createWidget
 *       и досрочную регистрацию всех скриптов
 */
class EThumbCarousel extends CWidget
{
    /**
     * @var array - список миниатюр картинок (внизу)
     */
    public $previews;
    /**
     * @var array - список средних фотографий в (отображаются в основном разделе, листаются)
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
     * @var bool - как отобразить скрипты
     *         true  - вывести скрипты вместе с html-кодом галереи (используется при загрузке виджета через AJAX)
     *         false - подключить скрипты в заголовке страницы (используется при обычном отображении виджета)
     */
    public $echoScripts = false;
    /**
     * @var string - текст, который выводится, когда нет ни одного изображения
     */
    public $emptyText;
    /**
     * @var string
     */
    protected $_assetUrl;
    /**
     * @var string
     */
    protected $_scripts;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // регистрируем скрипты и стили
        $this->_assetUrl = Yii::app()->assetManager->publish(
                        Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
                        'ECMarkup' . DIRECTORY_SEPARATOR .
                        'EThumbCarousel' . DIRECTORY_SEPARATOR .
                        'assets'   . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/EThumbCarousel.css');
        //Yii::app()->clientScript->registerScriptFile($this->_assetUrl.'/EThumbCarousel.js');
        
        $previews = array();
        foreach ( $this->previews as $id=>$preview )
        {// создаем скрипты, которые меняют большую картинку при клике на маленькую
            $setPhotoScriptId = '_'.$this->id.'ChangePhoto#'.$preview['num'];
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
            $this->photos[$id]['imageOptions'] = array(
                'id'    => $this->id.'-photo-'.$photo['id'],
                'style' => 'margin-left:auto;margin-right:auto;cursor:pointer;border-radius:10px;',
            );
        }
    }
    
    /**
     * 
     * @return null
     */
    public function registerWigetScripts()
    {
        
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
        $dataProvider = new CArrayDataProvider($this->previews, array(
            'pagination' => false,
            )
        );
        $this->widget('bootstrap.widgets.TbThumbnails', array(
                        'dataProvider' => $dataProvider,
                        'template'     => "{items}",
                        'itemView'     => '_thumb',
                        'emptyText'    => $this->emptyText,
                        )
        );
        // fancybox для каждой фотографии, чтобы можно было посмотреть увеличенную копию
        foreach ( $this->largePhotos as $id=>$photo )
        {
            $this->widget('application.extensions.fancybox.EFancyBox', array(
                            'target'=> '#'.$this->id.'-photo-'.$photo['id'],
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