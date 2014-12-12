<?php
/**
 * Контроллер для отображения новостей пользователю
 * @todo не используется - включить или удалить, в зависимости от того будем мы использовать
 *       на сайте новости или нет
 */
class NewsController extends Controller
{
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'News';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('news.models.News');
    }
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
            array(
                'ext.bootstrap.filters.BootstrapFilter',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Отображение всех новостей
     */
    public function actionIndex()
    {
        //$this->render('//news/index');
        $this->redirect(Yii::app()->createUrl('//site/index'));
    }
    
    /**
     * Просмотр одной новости
     * @param int $id - id новости
     */
    public function actionView($id)
    {
        $newsItem = $this->loadModel($id);
        
        $this->render('/news/view', array(
            'newsItem' => $newsItem,
        ));
    }
}