<?php
/**
 * Страница просмотра одной анкеты
 */
/* @var $this        QuestionaryController */
/* @var $questionary Questionary */

Yii::import('application.modules.catalog.CatalogModule');
Yii::import('application.modules.catalog.models.*');

$this->breadcrumbs = array(
    $questionary->user->fullname
);
// Кнопка "редактировать"
$editIcon = '';
if ( $canEdit )
{// анкету пользователя может редактировать только админ и сам пользователь
    $editIcon = CHtml::link(Yii::t('coreMessages', 'edit'), 
        Yii::app()->createUrl('//questionary/questionary/update', array('id' => $questionary->id)),
        array('class' => 'btn btn-warning btn-large'));
}
// выводим оповещения
$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="row-fluid">
    <?php 
    // предупреждение о том что нужно дополнить данные заявки (если надо)
    $this->widget('questionary.extensions.widgets.QUserNotifications.QUserNotifications', array(
        'questionary' => $questionary,
    ));
    ?>
    <div id="order_message" class="<?= $orderMessageClass; ?>" style="margin-top:20px;<?= $orderMessageStyle; ?>">
        <?php 
        // сообщение о том что участник приглашен на съемки
        echo $orderMessage; 
        echo '<br>';
        echo $dismissButton;
        ?>
    </div>
    <h2 class="pull-right">
        <?php 
        // Имя, фамилия, возраст 
        echo $questionary->user->fullname;
        if ( $questionary->age )
        {
            echo ', '.$questionary->age;
        }
        // кнопки "редактировать" и "пригласить" 
        echo '&nbsp;'.$editIcon.$inviteButton;
        ?>
    </h2>
</div>
<div class="row-fluid">
    <div class="span5">
        <?php 
        // Список фото и видео
        $this->widget('questionary.extensions.widgets.QUserMedia.QUserMedia', array(
            'questionary' => $questionary,
        ));
        if ( ! $questionary->visible )
        {// Сообщение о том что анкета скрыта
            // @todo включить после добавления настроек
            /*$this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => QuestionaryModule::t('your_profile_is_hidden'),
            ));*/
        }
        ?>
    </div>
    <div class="span7">
        <div class="row-fluid">
            <?php 
            // выводим список умений и достижений участника 
            $this->widget('questionary.extensions.widgets.QUserBages.QUserBages', array(
                'bages' => $questionary->bages,
            ));
            ?>
        </div>
        <div class="row-fluid">
            <?php 
            // Выводим всю остальную информацию о пользователе
            $this->widget('questionary.extensions.widgets.QUserInfo.QUserInfo', array(
                'questionary' => $questionary,
                'placement'   => 'right',
                'activeTab'   => $activeTab,
            ));
            ?>
        </div>
    </div>
</div>
<?php  
if ( Yii::app()->user->checkAccess('Admin') )
{// если админы вводят много анкет подряд - упростим им задачу, добавив кнопку внизу
    $newUserLink = CHtml::link('<i class="icon-plus icon-large"></i>&nbsp;Добавить еще анкету', 
        Yii::app()->createUrl('//user/admin/create'/*, array('lastId' => $questionary->id)*/),
        array('class' => 'btn btn-large btn-warning'));
    // и в самом низу добавим кнопку "зайти под этим участником"
    $loginAsLink = CHtml::link('<i class="icon-user icon-large"></i>&nbsp;Зайти под этим участником',
        Yii::app()->createUrl('//questionary/questionary/loginAs', array('id' => $questionary->id)),
        array('class' => 'btn btn-large'));
    echo '<div class="row-fluid text-center"><br>'.$newUserLink.'&nbsp;'.$loginAsLink.'</div>';
}
?>
<div class="container">
    <?php
    // список приглашений участника
    if ( $canEdit OR Yii::app()->user->checkAccess('Admin') )
    {// отображается или самому участнику или админу
        $this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $questionary,
        ));
    }
    //$params = $questionary->getConfig();
    //CVarDumper::dump($params, 10, true);
    //CVarDumper::dump(Config::model()->findByPk(20), 10, true);
    
    //$paramObjs = Config::model()->forModel($questionary)->findAll();
    //CVarDumper::dump($paramObjs, 10, true);
    
    //$obj = current($paramObjs);
    //$obj = Config::model()->findByPk(211);
    //CVarDumper::dump($obj, 10, true);
    //CVarDumper::dump($obj->getDefaultValue(), 10, true);
    //CVarDumper::dump($obj->value, 10, true);
    
    ?>
</div>