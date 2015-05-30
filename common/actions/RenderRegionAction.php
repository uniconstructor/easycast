<?php

namespace common\actions;

use Yii;
use yii\base\Action;
use yii\base\UserException;
use yii\web\NotFoundHttpException;

/**
 * Действие контроллера отображающее один регион cockpit
 * Регион ищется по служебному названию (slug)
 * 
 * @todo проверка параметров
 * @todo все варианты отрисовки
 * @todo поиск региона по id и name
 * @todo проверить все ли параметры переданы
 */
class RenderRegionAction extends Action
{
    /**
     * @var string - название региона
     */
    public $region;
    /**
     * @var array - данные необходимые для отображения региона
     */
    public $regionData = [];
    /**
     * @var string
     */
    public $layout;
    /**
     * @var string
     */
    public $render = 'content';
    /**
     * @var string
     */
    public $view;
    /**
     * @var string
     */
    public $theme;
    /**
     * @var closure
     */
    public $beforeRender;
    /**
     * @var closure
     */
    public $afterRender;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Найти и отобразить регион
     * 
     * @return string 
     */
    public function run()
    {
        $slug         = Yii::$app->request->getQueryParam('region');
        $regionData = Yii::$app->request->getQueryParam('data', $this->regionData);
        
        if ( ! $slug OR ! $region = \cockpit('regions:get_region_by_slug', $slug) )
        {
            throw new NotFoundHttpException('Не найден отображаемый регион: {'.$slug.'}');
        }
        // получаем готовый html региона
        $html = \get_region($region['name'], $regionData);
        $data = [
            'region' => $region,
            'html'   => $html,
        ];
        switch ( $this->render )
        {// отображаем регион в зависимости от настроек вывода
            case 'raw':     return $html;
            case 'content': return $this->controller->renderContent($html);
            case 'layout':  return $this->controller->render($this->view, $data);
            case 'partial': return $this->controller->renderPartial($this->view, $data);
            case 'ajax':    return $this->controller->renderAjax($this->view, $data);
            case 'file':    return $this->controller->renderFile($this->view, $data);
        }
        Yii::trace('Unknown render method used');
        return $html;
    }
}
