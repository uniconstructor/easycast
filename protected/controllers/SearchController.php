<?php
/**
 * Контроллер для работы с краткой и расширенной формой поиска
 */
class SearchController extends Controller
{
    /**
     * @var string - верстка всех страниц поиска (без меню)
     */
    public $layout = '//layouts/column1';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('catalog.models.*');
        parent::init();
    }
    
    /**
     * Отображает расширенную форму поиска
     * При обработке поискового запроса перенаправляет пользователя на страницу поиска в каталоге
     * (та которая со списком поисковых фильтров справа)
     * @return void
     */
    public function actionIndex()
    {
        $this->render('search');
    }
}