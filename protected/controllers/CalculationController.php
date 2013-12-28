<?php

/**
 * Контроллер для заказа рассчета стоимости
 * @todo перенести в главный контроллер сайта после того как станет ясно как настроить rewriteRule
 *       таким образом, чтобы обращение по короткому адресу easycast.ru/calculation
 *       приводило к выполнению действия SiteController 
 * @todo подставлять автоматически email и имя при заходе по реферал-ссылке из комерческого предложения
 */
class CalculationController extends Controller
{
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        Yii::import('catalog.models.*');
        parent::init();
    }
    
    /**
     * Отобразить и обработать форму рассчета стоимости
     * @return void
     */
    public function actionIndex()
    {
        $calculationForm = new CalculationForm();
        
        // AJAX-проверка введенных данных
        $this->performAjaxValidation($calculationForm);
        
        // получаем все возможные типы проекта
        $projectTypes = Project::model()->getTypeList();
        // получаем все возможные разделы каталога
        $criteria = new CDbCriteria();
        $criteria->compare('parentid', 1);
        $criteria->compare('visible', 1);
        
        $sections = CatalogSection::model()->findAll($criteria);
        $categories = array();
        foreach ($sections as $section)
        {
            //$categories[$section->id] = $section->name;
            $categories[$section->name] = $section->name;
        }
        $categories = ECPurifier::getSelect2Options($categories);
        
        if ( $formData = Yii::app()->request->getPost('CalculationForm') )
        {// пришли данные из формы расчета стоимости
            $calculationForm->attributes = $formData;
            //CVarDumper::dump($formData, 10, true);die;
            
            if ( $calculationForm->validate() )
            {// все данные формы верны
                if ( $user = $calculationForm->save() )
                {// сохранение удалось
                    // добавляем flash-сообщение
                    Yii::app()->user->setFlash('success', '<br>Ваш запрос принят.<br>
                        Мы расчитаем стоимость съемки и пришлем результаты вам на почту <b>'.$calculationForm->email.'</b>.');
                    
                    // и перенаправляем его на страницу просмотра своей анкеты
                    $this->redirect('/calculation/success');
                }
            }
        }
        
        $this->render('calculation', array(
            'categories'      => $categories,
            'projectTypes'    => $projectTypes,
            'calculationForm' => $calculationForm,
        ));
    }
    
    /**
     * Отобразить сообщение после принятия заказа на расчет стоимости
     * @return void
     */
    public function actionSuccess()
    {
        $this->render('success');
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) && $_POST['ajax'] === 'calculation-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}