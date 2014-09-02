<?php
/**
 * Краткая информация об участнике
 */
/* @var $this MpMemberData */

// ссылка на анкету участника
$qUrl = Yii::app()->createUrl('/questionary/questionary/view/', array('id' => $this->questionary->id));
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
            if ( Yii::app()->user->checkAccess('Admin') )
            {// @todo кнопка загрузки файлов видео: пока что видео привязывается к анкете а не к заявке
                /*$xUploadForm = new XUploadForm;
                $this->widget('xupload.XUpload', array(
                    'url'             => Yii::app()->createUrl("//questionary/questionary/upload", array('objectId' => $this->questionary->id)),
                    'model'           => $xUploadForm,
                    'attribute'       => 'file',
                    'autoUpload'      => true,
                    'previewImages'   => false,
                    'imageProcessing' => false,
                    'multiple'        => false,
                ));*/
                $expires = '+48 hours';
            }else
            {// @todo сделать время жизни ссылки на видео равным времени жизни приглашения заказчика
                $expires = '+48 hours';
            }
            // список загруженных видео
            $this->widget('ext.ECMarkup.ECUploadedVideo.ECUploadedVideo', array(
                'objectType' => 'questionary',
                'objectId'   => $this->questionary->id,
                //'expires'    => $expires,
            ));
            ?>
        </div>
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
            'questionary' => $this->questionary,
            'placement'   => 'above',
        ));
        // блок со статусами
        $this->render('_statuses');
        
        echo 'Дата создания заявки: '.date('Y-m-d H:i', $this->member->timecreated);
        ?>
    </div>
</div>