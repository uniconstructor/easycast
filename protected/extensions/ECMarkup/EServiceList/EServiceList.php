<?php

/**
 * Виджет "список услуг" - выводит список всех услуг в виде фотокарточек
 * Каждое изображение является ссылкой на соответствующий раздел
 * 
 * @todo добавить возможность включения/отключения анимации картинок при наведении
 * @todo добавить возможность добавлять css3-подписи при наведении  на картинку
 * @todo добавить возможность выбирать какие услуги отображать
 * @todo при создании новых разделов каталога включить их в этот список, вместо ссылки на рассчет стоимости
 */
class EServiceList extends CWidget
{
    /**
     * @var string - путь к папке со стилями и скриптами виджета
     */
    protected $assetUrl;
    
    /**
     * @see CWidget::run()
     */
    public function init()
    {
        $this->assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('ext.ECMarkup.EServiceList.assets').DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->assetUrl.'/eservicelist.css');
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('list');
    }
    
    /**
     * Получить ссылку на услугу (как правило это раздел каталога или ссылка на заявку на расчет стоимости)
     * @param string $service - короткое название услуги 
     *                          (совпадает с названием раздела каталога, если есть соответствующий раздел каталога)
     * @return string - ссылка на услугу
     */
    protected function getServiceLink($service)
    {
        $sectionId = 0;
        $link      = '#';
        
        switch ( $service )
        {
            case 'media_actors':        $sectionId = 2; break;
            case 'professional_actors': $sectionId = 4; break;
            case 'models':              $sectionId = 3; break;
            case 'children_section':    $sectionId = 5; break;
            case 'mass_actors':         $sectionId = 17; break;
            case 'emcees':              $sectionId = 8; break;
            case 'singers':             $sectionId = 9; break;
            case 'dancers':             $sectionId = 11; break;
            case 'musicians':           $sectionId = 10; break;
        }
        
        if ( $sectionId )
        {
            $link = Yii::app()->createAbsoluteUrl('//catalog/catalog/index', array('sectionid' => $sectionId));
        }else
        {
            $link = Yii::app()->createAbsoluteUrl('//order/index', array('service' => $service));
        }
        
        return $link;
    }
}