<?php

/**
 * Разметка для редактора текста со стандартным оповещением
 */
/* @var $this SimpleEmailRedactor */
/* @var $form TbActiveForm */
?>
<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <div class="span12">
                <h2 class="text-center">Изменить текст оповещения</h2>
                <?php 
                $url = Yii::app()->createUrl($this->updateUrl, array('id' => $this->config->valueid));
                $FormOptions = array(
                    'id'     => 'notify-config-form-'.$this->id,
                    'method' => 'post',
                    'action' => $url,
                    'enableAjaxValidation' => true,
                );
                // форма редактирования письма
                $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', $FormOptions);
                // @todo редактор по умолчанию будет содержать текст стандартного оповещения
                echo $form->html5EditorRow($this->configValue, 'value', array(
                    'editorOptions' => array(
                        'class'   => 'span4',
                        'rows'    => 5,
                        'height'  => '300',
                        'options' => array('color' => false),
                    )
                ));
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'submit',
                    'type'       => 'primary',
                    'label'      => 'Сохранить',
                ));
                $this->endWidget();
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <?php 
                // @todo ссылка чтобы сделать все как было
                /*$restoreUrl = Yii::app()->createUrl('admin/eventVacancy/restoreDefault', array(
                    'id' => $this->config->objectid,
                    'restoreDefault' => 1,
                ));
                echo CHtml::link('Вернуть исходный текст', $restoreUrl, array(
                    'class'   => 'btn btn-warning btn-large',
                    'confirm' => 'Восстановить стандартный вид оповещения?
                        Все внесенные изменения будут потеряны.',
                ));*/
                ?>
            </div>
            <div class="span6">
                <?php 
                // ссылка на предпросмотр
                $previewUrl = Yii::app()->createUrl('admin/admin/restoreDefault', array(
                    'id' => $this->config->valueid,
                ));
                echo CHtml::link('Предварительный просмотр', $previewUrl, array(
                    'class'  => 'btn btn-success btn-large pull-right',
                    'target' => '_blank',
                ));
                ?>
            </div>
        </div>
    </div>
</div>