<?php

/**
 * Контроллер для быстрой регистрации 
 */
class EasyController extends Controller
{
    /**
     * Быстрая регистрация массовки
     * Этот метод регичтрации использует одну хитрость при работе с галереей: поскольку мы не можем сохранить
     * фотографии галереии без объекта, к котому она привязана, то мы сначала создаем галерею отдельно
     * (не привязанную ни к чему), затем создаем пользователя и анкету, и только после этого подставляем
     * созданную галерею в объект анкеты 
     * @return void
     * 
     * @todo брать настройки галереи из модуля Questionary
     * @todo обработать возможные ошибки
     */
    public function actionIndex()
    {
        //$this->redirect('/user/registration');
        // Создаем форму для регистрации массовки
        $massActorForm = new MassActorsForm();
        
        $this->performAjaxValidation($massActorForm);
        
        if ( $formData = Yii::app()->request->getPost('MassActorsForm') )
        {
            $massActorForm->attributes = $formData;
            //var_dump($formData);die;
            $gallery = Gallery::model()->findByPk($massActorForm->galleryid);
            if ( $massActorForm->validate() )
            {
                
            }
        }else
        {// Создаем пустую галерею
            $gallery = new Gallery();
            // Определяем в каких размерах созлдавать миниатюры изображений в галерее
            $gallery->versions    = $this->createDefaultGalleryVersions();
            $gallery->limit       = 30;
            $gallery->name        = 1;
            $gallery->description = 1;
            $gallery->save(false);
            $massActorForm->galleryid = $gallery->id;
        }
        
        
        // Отображаем страницу формы с регистрацией массовки
        $this->render('index', array(
            'gallery'       => $gallery,
            'massActorForm' => $massActorForm,
        ));
        CVarDumper::dump($_POST, 10, true);
        
    }
    
    /**
     * 
     * @return array
     * @todo взять эти параметры из модуля Questionary
     */
    protected function createDefaultGalleryVersions()
    {
        return array(
            'small' => array(
                'centeredpreview' => array(100, 100),
            ),
            'medium' => array(
                'resize' => array(530, 330),
            ),
            'large' => array(
                'resize' => array(800, 1000),
            ),
            'catalog' => array(
                'centeredpreview' => array(150, 150),
            ),
        );
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) && $_POST['ajax']==='mass-actors-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}