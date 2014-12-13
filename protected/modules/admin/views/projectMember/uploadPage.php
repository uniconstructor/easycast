<?php
/**
 * Отдельная страница для загрузки видео для заявки
 */
/* @var $this        ProjectMemberController */
/* @var $member      ProjectMember */
/* @var $questionary Questionary */

$questionary = $member->questionary;

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Загрузка видео для заявки',
);

$userUrl  = Yii::app()->createUrl('/questionary/questionary/view', array('id' => $questionary->id));
$userLink = CHtml::link($questionary->fullname, $userUrl, array('target' => '_blank'));
?>
<div class="page">
    <h3 class="text-center"><?= $userLink; ?></h3>
    <div class="row-fluid">
        <div class="span5">
            <?php 
            // Список фото и видео
            $this->widget('questionary.extensions.widgets.QUserMedia.QUserMedia', array(
                'questionary' => $questionary,
            ));
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
                ));
                ?>
            </div>
        </div>
    </div>
    <h1 class="text-center">Загрузка видео</h1>
    <div class="row-fluid">
        <div class="span8 offset2 text-center">
            <?php 
            // список загруженных видео
            // @todo включить формирование подписанных ссылок
            $this->widget('ext.ECMarkup.ECUploadedVideo.ECUploadedVideo', array(
                'objectType' => 'ProjectMember',
                'objectId'   => $member->id,
            ));
            // Xupload: загрузка видео на Amazon S3
            Yii::import("xupload.models.S3XUploadForm");
            $xUploadForm = new S3XUploadForm;
            $this->widget('xupload.XUpload', array(
                'url' => Yii::app()->createUrl("//admin/projectMember/upload", array(
                    'objectId'   => $member->id,
                    'objectType' => 'ProjectMember',
                )),
                'model'           => $xUploadForm,
                'attribute'       => 'file',
                'autoUpload'      => true,
                'previewImages'   => false,
                'imageProcessing' => false,
                'multiple'        => false,
            ));
            ?>
        </div>
    </div>
</div>