<?php

/**
 * Контроллер для редактирования опыта работы в театре
 * Используется динамической формой анкеты при редактировании сложных значений по AJAX
 * 
 * @package    easycast
 * @subpackage questionary
 */
class QTheatreInstanceController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QTheatreInstance';
    
    /**
     * @var int - максимальное количество театров, возвращаемых для выпадающего списка в элементе select2
     */
    const MAX_RESULTS = 15;
    
    /**
     * @todo настроить доступ на основе ролей
     *
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'delete'),
                'users'   => array('@'),
            ),
            array('allow', // позволяем всем получать общий список ВУЗов, чтобы гости могли пользоваться поиском
                'actions' => array('getTheatreList'),
                'users'   => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Отправить по AJAX список театров для выпадающего списка в элементе select2
     * @return void
     */
    public function actionGetTheatreList()
    {
        $term = Yii::app()->request->getParam('term', '');
        $qid  = Yii::app()->request->getParam('qid', 0);
    
        $models   = $this->getModels($term);
        $listData = CHtml::listData($models, 'id', 'name');
        $options  = ECPurifier::getSelect2Options($listData);
    
        echo CJSON::encode($options);
    }
    
    /**
     * Получить список музыкальных или театральных ВУЗов
     * @param string $term - первые буквы названия университета
     * @return QUniversity
     *
     * @throws CException
     *
     * @todo удалить из базы все театры с кавычками и оставить LIKE только справа
     * @todo оставить только одобренные театры
     */
    protected function getModels($term)
    {
        $criteria = new CDbCriteria();
        $criteria->limit = self::MAX_RESULTS;
        $criteria->order = "`name` ASC";
        $criteria->addSearchCondition('name', $term);
        
        return QTheatre::model()->findAll($criteria);
    }
}