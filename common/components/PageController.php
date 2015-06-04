<?php

namespace common\components;

use Yii;
use yii\base\Controller;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\actions\RenderRegionAction;

/**
 * Контроллер для отображения страниц созданных вручную из админки
 */
class PageController extends Controller
{
    /**
     * @var string
     */
    public $collectionSlug = 'yii-controller-actions';
    
    /**
     * @var array
     */
    protected $customActions = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->customActions = $this->loadCustomActions();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [];
        
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = [];
        
        foreach ( $this->customActions as $data )
        {
            $actions[$data['name']] = [
                'class'  => RenderRegionAction::className(),
                'region' => $data['region'],
                'render' => 'content',
            ];
            if ( isset($data['layout']) AND $data['layout'] )
            {
                $actions[$data['name']]['layout'] = $data['layout'];
            }
        }
        return $actions;
    }
    
    /**
     * Загрузить созданные страницы из коллекции
     * 
     * @return array
     */
    protected function loadCustomActions()
    {
        $collection = cockpit('collections:get_collection_by_slug', $this->collectionSlug);
        $actions    = $collection->find()->toArray();
        
        return $actions;
    }
}
