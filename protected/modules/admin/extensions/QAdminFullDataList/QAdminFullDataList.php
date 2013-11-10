<?php

/**
 * Список заявок на участие, в которых отображена полная информация по каждому участнику
 * 
 * @todo рефакторинг: сделать этот виджет наследником общего класса отображения информации об участниках
 *       (совместить с классом MyChoice, сделать возможность гибко настраивать краткий/полный внешний вид)
 */
class QAdminFullDataList extends CWidget
{
    /**
     * @var Questionary[] - массив анкет пользователей 
     */
    public $questionaries;
    
    /**
     * @var array - массив отображаемых заявок участников
     */
    public $members;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // Подключаем все классы данных анкеты
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        // Подключаем виджеты для отображения подробной информации по анкете
        Yii::import('application.modules.questionary.extensions.widgets.QUserInfo.QUserInfo');
        Yii::import('application.modules.questionary.extensions.widgets.QUserBages.QUserBages');
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $elements = array();
        foreach ( $this->members as $member )
        {// перебираем все добавленные в заказ анкеты и извлекаем из них все данные
            // необходимые для отображения краткой информации
            $elements[$member->id] = $this->getShortInfo($member);
        }
        
        // Создаем источник данных для вывода краткой информации
        $dataProvider = new CArrayDataProvider($elements, 
            array(
                'pagination' => false,
            )
        );
        
        // отображаем всех добавленных в заказ пользователей
        $this->widget('bootstrap.widgets.TbListView', array(
            'dataProvider' => $dataProvider,
            'itemView' => 'application.modules.admin.extensions.QAdminFullDataList.views._orderItem',
        ));
    }
    
    /**
     * Отобразить краткую информацию о пользователе в заказе
     * Самые общие данные + кнопки "удалить" и "вся информация"
     *
     * @param ProjectMember $member
     * @return null
     */
    public function getShortInfo($member)
    {
        $questionary = $member->questionary;
        // Служебные данные (для работы скриптов и т. п.)
        $info = array('id' => $questionary->id);
        // id тега в котором содержится вся информация об участнике (и краткая и полная)
        $info['baseContainerId']      = $this->getContainerId($questionary->id, 'base');
        // id тега с полной информацией
        $info['fullInfoContainerId']  = $this->getContainerId($questionary->id, 'fullinfo');
        // id тега с краткой информацией
        $info['shortInfoContainerId'] = $this->getContainerId($questionary->id, 'shortinfo');
        // id тега с сообщением
        $info['messageContainerId']   = $this->getContainerId($questionary->id, 'message');
        // id hidden-элемента, в котором хранится состояние данных анкеты актера (подгружены/не подгружены)    
        $info['fullInfoLoadedId'] = 'full_info_loaded_'.$questionary->id; 
    
        // Имя
        $qUrl = Yii::app()->createAbsoluteUrl('/questionary/questionary/view', array('id' => $questionary->id));
        $fullName = CHtml::link($questionary->fullname, $qUrl, array('target' => '_blank'));
        $info['fullName'] = $fullName;
    
        // Возраст
        $info['age'] = '';
    
        // Аватарка участника (150*150)
        // (ссылка с картинки ведет на его анкету, открывается в новом окне)
        $imageURL   = $questionary->getAvatarUrl('catalog');
        $image      = CHtml::image($imageURL, '', array('class' => 'ec-rounded-avatar'));
        $info['profileUrl'] = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl,
            array('id' => $questionary->id));
        $info['avatar'] = CHtml::link($image, $info['profileUrl'], array('target' => '_blank'));
    
        // достижения (основные характеристики) участника
        $info['bages'] = $this->widget('application.modules.questionary.extensions.widgets.QUserBages.QUserBages', array(
            'bages' => $questionary->bages,
        ), true);
    
        // Кнопка запроса полной информации
        $info['fullInfoButton'] = $this->createAjaxButton('fullinfo', $questionary->id);
        // Кнопка возврата к краткой информации
        $info['shortInfoButton'] = $this->createShortInfoButton($questionary->id);
        
        // кнопки действий
        $info['actionButtons'] = $this->getMemberActions($member);
        
        return $info;
    }
    
    /**
     * Получить кнопки с действиями для участника
     * @param ProjectMember $member
     * @return string
     */
    protected function getMemberActions($member)
    {
        return $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', array(
            'member' => $member,
        ), true);
    }
    
    /**
     * Создать AJAX-кнопку для удаления, восстановления или запроса полной информации об участнике
     * @param string $type - тип кнопки 'delete', 'restore', 'fullinfo'
     * @param int $id - id отображаемой анкеты
     * @return string
     */
    protected function createAjaxButton($type, $id)
    {
        // Получаем надпись, настройки AJAX и адрес запроса для каждой кнопки
        $caption     = $this->getButtonCaption($type);
        $ajaxOptions = $this->createAjaxOptions($type, $id);
        $htmlOptions = $this->createHtmlOptions($type, $id);
        $url = $this->getAjaxUrl($type, $id);
        // Делаем из всего этого кнопку, выполняющую AJAX-запрос
        return CHtml::ajaxLink($caption, $url, $ajaxOptions, $htmlOptions);
    }
    
    /**
     * Создать кнопку скрывающую полную информацию об анкете обратно до краткой
     * @param int $id - id отображаемой анкеты
     * @return string
     */
    protected function createShortInfoButton($id)
    {
        $js = $this->createShortInfoJS($id);
        Yii::app()->clientScript->registerScript('_ecShortInfoJS#'.$id, $js, CClientScript::POS_END);
        return CHtml::htmlButton($this->getButtonCaption('shortinfo'), $this->createHtmlOptions('shortinfo', $id));
    }
    
    /**
     * Задать параметры AJAX-запроса при нажатии на кнопку
     * @param $type - тип кнопки 'delete', 'restore', 'fullinfo'
     * @param int $id - id отображаемой анкеты
     * @return array
     * 
     * @todo не дублировать JS-код, а просто зарегистрировать по одному экземпляру каждой функции
     *        и обращаться к ним с каждой анкеты 
     * @todo обработать все случаи AJAX-ошибок
     */
    protected function createAjaxOptions($type, $id)
    {
        $ajaxOptions = array(
            'url'  => $this->getAjaxUrl($type, $id),
            'data' => array(
                'id' => $id,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
            'dataType' => 'json',
            'type'     => 'post',
            //'error'  =>
        );
        if ( $type == 'fullinfo' )
        {// Запрос полной информации по участнику возвращает HTML вместо json
            $ajaxOptions['dataType'] = 'html';
            $ajaxOptions['data']['displayType'] = 'myChoice';
            // при запросе полной информации проверим, не загружена ли она уже
            $ajaxOptions['beforeSend'] = $this->createBeforeSendFullInfoJS($id);
            // и если не загружена - то загрузим ее AJAX-запросом
            $ajaxOptions['success']    = $this->createFullInfoSuccessJS($id);
        }
        if ( $type == 'delete' )
        {// удаление участника из заказа
            $ajaxOptions['success'] = $this->createDeleteSuccessJS($id);
        }
        if ( $type == 'restore' )
        {// восстановление участника обратно после удаления
            $ajaxOptions['success'] = $this->createRestoreSuccessJS($id);
        }
        
        return $ajaxOptions;
    }
    
    /**
     * Получить HTML-свойства для каждой кнопки в анкете
     * @param string $type - тип запроса 'delete', 'restore', 'fullinfo', 'shortinfo'
     * @param int $id - id отображаемой анкеты
     * @return array
     */
    protected function createHtmlOptions($type, $id)
    {
        $options = array(
            'class' => $this->getButtonClass($type),
            'id'    => $this->getButtonId($type, $id)
        );
        
        if ( $type == 'shortinfo' OR $type == 'restore' )
        {// Кнопки возврата обратно к краткой информации и 
            // восстановления удаленной из заказа анкеты изначально скрыты
            // и появляются только после соответствующих действий
            $options['style'] = 'display:none;';
        }
        if ( $type == 'fullinfo' )
        {
            $options['data-loading-text'] = "<i class='icon-spinner icon-spin icon-large'></i> Загрузка...";
        }
        
        return $options;
    }
    
    /**
     * Получить Url для отправки ajax-запроса (для удаления или восстановления участника в заказе
     * или для получения полной информации по нему)
     * @param string $type - тип запроса 'delete', 'restore', 'fullinfo'
     * @param int $id - id отображаемой анкеты
     * @return string
     */
    protected function getAjaxUrl($type, $id)
    {
        switch ( $type )
        {
            case 'delete': 
                return Yii::app()->createUrl('//questionary/questionary/dismiss', array('id' => $id));
            break;
            case 'restore':
                return Yii::app()->createUrl('//questionary/questionary/invite', array('id' => $id));
            break;
            case 'fullinfo':
                return Yii::app()->createUrl('//questionary/questionary/ajaxGetUserInfo', 
                            array('id' => $id, 'displayType' => 'myChoice')); 
            break;
        }
    }
    
    /**
     * Скрипт, срабатывающий после удаления участника из заказа 
     * 
     * Убирает краткую и полную информацию и заменяет ее на сообщение об удалении
     * Добавляет кнопку восстановления 
     * Убирает отработавшую кнопку удаления
     * 
     * @param  int $id - id отображаемой анкеты
     * @return string
     */
    protected function createDeleteSuccessJS($id)
    {
        $message = $this->getMessageAfterDelete();
        
        $fullContainerId  = $this->getContainerId($id, 'fullinfo');
        $shortContainerId = $this->getContainerId($id, 'shortinfo');
        $messageContainerId = $this->getContainerId($id, 'message');
        
        $shortInfoButtonId = $this->getButtonId('shortinfo', $id);
        $fullInfoButtonId  = $this->getButtonId('fullinfo', $id);
        $restoreButtonId   = $this->getButtonId('restore', $id);
        $deleteButtonId    = $this->getButtonId('delete', $id);
        
        return "function(data, status){
            $('#{$fullContainerId}').fadeOut(200);
            $('#{$shortContainerId}').hide();
            $('#{$messageContainerId}').show();
            $('#{$messageContainerId}').html('{$message}');
            
            $('#{$shortInfoButtonId}').fadeOut(200);
            $('#{$fullInfoButtonId}').fadeOut(200);
            $('#{$deleteButtonId}').fadeOut(200);
            $('#{$restoreButtonId}').fadeIn(200);
        }";
    }
    
    /**
     * Скрипт, срабатывающий после восстановления удаленной из заказа анкеты
     * 
     * Восстанавливает отображение краткой информации
     * Убирает сообщение об удалении
     * Убирает отработавшую кнопку восстановления
     * 
     * @param  int $id - id отображаемой анкеты
     * @return string
     */
    protected function createRestoreSuccessJS($id)
    {
        $fullContainerId  = $this->getContainerId($id, 'fullinfo');
        $shortContainerId = $this->getContainerId($id, 'shortinfo');
        $messageContainerId = $this->getContainerId($id, 'message');
        
        $fullInfoButtonId  = $this->getButtonId('fullinfo', $id);
        $restoreButtonId   = $this->getButtonId('restore', $id);
        $deleteButtonId    = $this->getButtonId('delete', $id);
        
        return "function(data, status){
            $('#{$shortContainerId}').show();
            $('#{$messageContainerId}').hide();
            
            $('#{$fullInfoButtonId}').fadeIn(200);
            $('#{$restoreButtonId}').fadeOut(200);
            $('#{$deleteButtonId}').fadeIn(200);
        }";
    }
    
    /**
     * Получить JS-код для обработки запроса подробной информации об анкете
     * 
     * Заменяет кнопку "Подробнее..." на "Свернуть"
     * Загружает пришедшие из AJAX данные в контейнер
     * 
     * @param int $id - id отображаемой анкеты
     * @return string
     * 
     * @todo возможно следует устанавливать какую-нибудь скрытую переменную, которая отмечает
     *        что для этой анкеты данные уже загружены и больше не надо
     */
    protected function createFullInfoSuccessJS($id)
    {
        // получаем id контейнера, в который будем загружать подробную информацию
        $fullContainerId = $this->getContainerId($id, 'fullinfo');
        
        // Получаем id кнопок для полной и для краткой информации
        $fullInfoButtonId  = $this->getButtonId('fullinfo', $id);
        $shortInfoButtonId = $this->getButtonId('shortinfo', $id);
        
        return "function(data, status){
            $('#{$fullContainerId}').html(data);
            $('#{$fullContainerId}').fadeIn(200);
            
            $('#{$fullInfoButtonId}').hide();
            $('#{$shortInfoButtonId}').show();
            
            $('#full_info_loaded_{$id}').val(1);
        }";
    }
    
    /**
     * Получить JS для сворачивания полной информации анкеты обратно в краткую
     * 
     * Заменяет кнопку "Свернуть" на "Подробнее..."
     * 
     * @param int $id - id отображаемой анкеты
     * @return string
     */
    protected function createShortInfoJS($id)
    {
        // получаем id контейнера, в котором хранится загруженная подробная информация
        $fullContainerId = $this->getContainerId($id, 'fullinfo');
        
        // Получаем id кнопок для полной и для краткой информации
        $fullInfoButtonId  = $this->getButtonId('fullinfo', $id);
        $shortInfoButtonId = $this->getButtonId('shortinfo', $id);
        
        // Скрываем подробную информацию, убираем кнопку "свернуть", восстанавливаем кнопку "подробнее"
        $function = "function(){
            $('#{$fullContainerId}').fadeOut(200);
            $('#{$shortInfoButtonId}').fadeOut(200);
            $('#{$fullInfoButtonId}').fadeIn(200);
        }";
        
        return "$('#{$shortInfoButtonId}').click({$function});";
    }
    
    /**
     * Получить JS-код, который проверяет, были ли уже загружены по AJAX полные данные анкеты,
     *                  и если да - то отменяет AJAX-запрос, и показывает ранее загруженные
     *                  но скрытые данные
     *                  
     * @param int $id - id отображаемой анкеты
     * @return string
     * 
     * @todo исправить скрипт так, чтобы не загружать информацию когда она уже есть
     */
    protected function createBeforeSendFullInfoJS($id)
    {
        $containerId = $this->getContainerId($id, 'fullinfo');
        
        $fullInfoButtonId  = $this->getButtonId('fullinfo', $id);
        $shortInfoButtonId = $this->getButtonId('shortinfo', $id);
        return "function(jqXHR, settings){
            if ( $('#full_info_loaded_{$id}').val() == 1 )
            {
                $('#{$containerId}').fadeIn(200);
                $('#{$fullInfoButtonId}').hide();
                $('#{$shortInfoButtonId}').show();
                return false;
            }
            return true;
        }";
    }
    
    /**
     * Получить уникальный html-id для AJAX-кнопки
     * @param string $type - тип запроса 'delete', 'restore', 'fullinfo', 'shortinfo'
     * @param int $id - id отображаемой анкеты
     * @return string
     */
    protected function getButtonId($type, $id)
    {
        return $type.'_order_item_'.$id;
    }

    /**
     * Получить надпись на кнопке
     * @param string $type
     * @return string
     */
    protected function getButtonCaption($type)
    {
        $icon = '';
        switch ( $type )
        {
            case 'fullinfo':  $icon = "<i class='icon-chevron-down icon-large'></i> "; break;
            case 'shortinfo': $icon = "<i class='icon-chevron-up icon-large'></i> "; break;
        }
        return $icon.CatalogModule::t($type.'_order_item_caption');
    }
    
    /**
     * Получить css-класс для отображаемой кнопки
     * @param string $type
     * @return string
     */
    protected function getButtonClass($type)
    {
        switch ( $type )
        {
            case 'fullinfo':  return 'btn btn-inverse'; break;
            case 'shortinfo': return 'btn btn-general'; break;
        }
    }
    
    /**
     * Получить оформленное сообщение после нажатия на кнопку "Удалить"
     * @return string
     */
    protected function getMessageAfterDelete()
    {
        // сообщение отображается только при удалении участника из заказа, заменяя информацию о нем
        // При разворачивании и сворачивании информации в анкете также не выводится никаких сообщений
        return '<div id="message_after_delete" class="alert alert-block">'.CatalogModule::t('order_after_delete_message').'</div>';
    }
    
    /**
     * Получить id тега внутри которого хранится вся информация о пользователе
     * @param int $id - id отображаемой анкеты
     * @param string $type - тип тега-контейнера:
     *                         base - родительский, внешний контейнер, который содержит внутри себя
     *                                и краткую и полную информацию об анкете
     *                         shortinfo - контейнер с краткой информацией об анкете (изначально не пустой)
     *                         fullinfo -  контейнер с полной информацией об анкете (при загрузке страницы пустой,
     *                                          подгружается через AJAX по запросу) 
     * @return string
     */
    protected function getContainerId($id, $type='base')
    {
        return 'order_'.$type.'_container_'.$id;
    }
}