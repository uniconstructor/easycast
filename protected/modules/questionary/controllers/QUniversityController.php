<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком музыкальных и театральных ВУЗов
 *
 * @package    easycast
 * @subpackage questionary
*/
class QUniversityController extends QComplexValueController
{
    /**
     * @var int - максимальное количество ВУЗов, возвращаемых для выпадающего списка в элементе select2
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
                'actions' => array('getUniversityList'),
                'users'   => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Отправить по AJAX список ВУЗов для выпадающего списка в элементе select2
     * @return void
     */
    public function actionGetUniversityList()
    {
        $term = Yii::app()->request->getParam('term', '');
        $type = Yii::app()->request->getParam('type', '');
        $qid  = Yii::app()->request->getParam('qid', 0);
        
        $models   = $this->getModels($term, $type);
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
     * @todo удалить из базы все ВУЗы с кавычками и оставить LIKE только справа
     * @todo оставить только одобренные ВУЗы
     */
    protected function getModels($term, $type)
    {
        $criteria = new CDbCriteria();
        $criteria->limit = self::MAX_RESULTS;
        $criteria->order = "`name` ASC";
        $criteria->addSearchCondition('name', $term);
        
        switch ( $type )
        {
            case QUniversity::TYPE_THEATRE: $criteria->compare('type', QUniversity::TYPE_THEATRE); break;
            case QUniversity::TYPE_MUSIC:   $criteria->compare('type', QUniversity::TYPE_MUSIC); break;
            default: throw new CException('Unknown university type: '.$type);
        }
        
        return QUniversity::model()->findAll($criteria);
    }
}