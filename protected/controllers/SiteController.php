<?php

/**
 * Главный контроллер сайта
 */
class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction'
            )
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->render('index');
        // $this->render('application.modules.user.views.user.login');
        // $this->redirect(Yii::app()->getModule('user')->loginUrl);
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ( $error = Yii::app()->errorHandler->error )
        {
            if ( Yii::app()->request->isAjaxRequest )
            {
                if ( ( defined('YII_DEBUG') and YII_DEBUG === true ) OR Yii::app()->user->checkAccess('Admin') )
                {
                    echo $error['message'];
                }else
                {
                    Yii::app()->end();
                }
            }else
            {
                $this->render('error', $error);
            }
        }
    }

    /**
     * Displays the contact page
     *
     * @todo Языковые строки
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ( isset($_POST['ContactForm']) )
        {
            $model->attributes = $_POST['ContactForm'];
            if ( $model->validate() )
            {
                Yii::import('application.modules.user.UserModule');
                $name = CHtml::encode($model->name);
                
                $subject = CHtml::encode($model->subject);
                $subject = '[EasyCast] (Обратная связь) ' . $subject;
                
                $body = 'От кого: ' . $name . ' ( ' . $model->email . ' ) <br>' . $model->body;
                
                UserModule::sendMail(Yii::app()->params['adminEmail'], $subject, $body);
                // mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
                Yii::app()->user->setFlash('contact', 'Благодарим за ваш отзыв');
                $this->refresh();
            }
        }
        $this->render('contact', array(
            'model' => $model
        ));
    }

    /**
     * Обработчик формы срочного заказа из главного меню
     *
     * @todo в будущем можно будет обрабатывать и отсылать все что пользователь набрал в каталоге
     */
    public function actionPlaceFastOrder()
    {
        if ( ! YII_DEBUG && ! Yii::app()->request->isAjaxRequest )
        {
            throw new CHttpException('403', 'Forbidden access.');
        }
        
        $model = new FastOrder();
        
        if ( $post = Yii::app()->request->getParam('FastOrder') )
        {
            $model->attributes = $post;
            $valid = $model->validate();
            if ( $valid )
            { // Заказ успешно зарегистрирован
                $model->status = 'active';
                $model->type = 'fast';
                $model->save();
                
                echo CJSON::encode(array(
                    'status' => 'success'
                ));
                Yii::app()->end();
            }else
            { // ошибка при регистрации заказа
                $error = CActiveForm::validate($model);
                if ( $error != '[]' )
                {
                    echo $error;
                }
                Yii::app()->end();
            }
        }
        Yii::app()->end();
    }

    /**
     * Обработчик формы обычного заказа со страницы "мой выбор"
     *
     * @todo записывать id заказчика, если он есть
     * @todo сохранять данные анкет полностью
     * @todo посчитать общую сумму заказа
     * @todo языковые строки
     */
    public function actionPlaceOrder()
    {
        if ( $formData = Yii::app()->request->getParam('FastOrder') )
        { // данные заказа пришли, создаем заказ
            $model = new FastOrder();
            $model->attributes = $formData;
            $model->status = 'active';
            $model->type = 'normal';
            $model->save();
            
            // заказ создан, добавляем в него данные анкет
            $orderData = array();
            $orderData['users'] = FastOrder::getPendingOrderUsers();
            $orderData['usersCount'] = count($orderData['users']);
            $model->saveOrderData($orderData);
            // заказ оформлен, данные сохранены, очищаем данные в сессии
            FastOrder::clearPendingOrder();
            $orderNum = '<b>' . $model->id . '</b>';
            
            // сообщаем пользователю что его заказ оформлен
            Yii::app()->user->setFlash('success', 
                '<h4 class="alert-heading">Ваш заказ оформлен</h4>
	            Его номер ' . $orderNum . '.<br>
	            Скоро мы свяжемся с вами для того чтобы уточнить детали.');
            
            // перенаправляем обратно на страницу "мой выбор"
            $this->redirect(Yii::app()->createUrl('/catalog/catalog/myChoice'));
        }
    }

    /**
     * Функция для поддержания сессии в рабочем состоянии, используется через AJAX для того чтобы при
     * долгом заполнении формы данные пользователя не пропадали
     *
     * @return null
     */
    public function actionKeepAlive()
    {
        echo 'OK';
        Yii::app()->end();
    }

    /**
     * Асинхронная загрузка плагинов всех социальных сетей (чтобы не тормозило открытие каждой страницы)
     * 
     * @return null
     *
     * @todo переместить настройки виджета в /config/main.php
     */
    public function actionLoadSocial()
    {
        $this->widget('application.extensions.ESocial.ESocial', 
            array(
                'renderAjaxData' => true,
                'style' => 'horizontal',
                'networks' => array(
                    // g+
                    'googleplusone' => array(
                        "size" => "medium",
                        "annotation" => "bubble"
                    ),
                    // В контакте
                    'vkontakte' => array(
                        'apiid' => Yii::app()->params['vkontakteApiId'],
                        'containerid' => 'vk_like',
                        'scriptid' => 'vkontakte-init-script',
                        'type' => 'button'
                    ),
                    // mail.ru и одноклассники (добавляются одной кнопкой)
                    'mailru' => array(
                        'type' => 'combo'
                    ),
                    // Твиттер
                    'twitter' => array(
                        'data-via' => ''
                    ),
                    // Facebook
                    'facebook' => array(
                        'href' => 'http://easycast.ru/', // asociate your page http://www.facebook.com/page
                        'action' => 'recommend', // recommend, like
                        'colorscheme' => 'light',
                        'width' => '140px'
                    )
                )
            ));
    }

    /**
     * Возвращает пустой документ с заголовком connection:close
     * Эта функция нужна для того, чтобы обходить баг Safari, связанный с загрузкой файлов через AJAX.
     * Подробнее здесь: http://airbladesoftware.com/notes/note-to-self-prevent-uploads-hanging-in-safari
     *
     * @return null
     */
    public function actionClose()
    {
        header("Connection: close");
        Yii::app()->end();
    }
}