<?php
/**
 * Краткая информация об участнике
 */
/* @var $this MpMemberData */

// ссылка на анкету участника
$qUrl = Yii::app()->createUrl('/questionary/questionary/view/', array('id' => $this->questionary->id));
// @todo сделать время жизни ссылки на видео равным времени жизни приглашения заказчика
$expires = '+48 hours';
?>
<div class="row-fluid">
    <div class="span6">
        <div class="row-fluid">
            <?php
            // Фото и видео участника
            $this->widget('questionary.extensions.widgets.QUserMedia.QUserMedia', array(
                'questionary' => $this->questionary,
            ));
            ?>
        </div>
        <div class="row-fluid">
            <?php
            // список загруженных видео
            // @todo включить формирование подписанных ссылок
            $this->widget('ext.ECMarkup.ECUploadedVideo.ECUploadedVideo', array(
                'objectType' => 'ProjectMember',
                'objectId'   => $this->member->id,
            ));
            ?>
        </div>
        <?php
        if ( Yii::app()->user->checkAccess('Admin') )
        {// кнопка загрузки файлов видео: пока что видео привязывается к анкете а не к заявке
            // @todo админам показываем виджет со ссылкой на страницу загрузки видео
            echo '<div class="row-fluid text-center">';
            $uploadUrl = Yii::app()->createUrl('/admin/projectMember/uploadPage', array(
                'id' =>  $this->member->id,
            ));
            echo CHtml::link('Загрузить', $uploadUrl, array(
                'class'  => 'btn btn-primary',
                'target' => '_blank',
            ));
            echo '</div>';
        }
        ?>
    </div>
    <div class="span6">
        <h2 class="text-center">
            <?= CHtml::link($this->questionary->fullname, $qUrl, array('target' => '_blank')); ?>, 
            <?= $this->questionary->age; ?>
        </h2>
        <?php 
        // краткая информация
        /*$this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $this->getSummaryData(),
            'attributes' => $this->getSummaryAttributes(),
        ));*/
        $this->widget('questionary.extensions.widgets.QUserInfo.QUserInfo', array(
            'questionary'     => $this->questionary,
            'placement'       => 'above',
            'displayContacts' => $this->displayContacts,
        ));
        // блок со статусами
        $this->render('_statuses');
        
        echo 'Дата создания заявки: '.date('Y-m-d H:i', $this->member->timecreated);
        ?>
    </div>
</div>