<?php

/**
 * 
 * @todo контроллер для обработки сокращенных ссылок вида easycast.ru/for/example
 */
class ForController extends Controller
{
    /**
     * @return void
     */
    /*public function actionIndex()
    {
        echo 'INDEX()';
    }*/
    
    /**
     * @return void
     */
    public function actionView()
    {
        //CVarDumper::dump($_POST, 10, true);
        //echo 'VIEW()';
    }
    
    /**
     * @see CController::missingAction()
     */
    public function missingAction($action)
    {
        //CVarDumper::dump($action, 10, true);
        //$this->actionView();
        //$this->forward($route)
    }
}