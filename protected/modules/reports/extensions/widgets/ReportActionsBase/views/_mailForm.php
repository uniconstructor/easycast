<?php
/**
 * Форма отправки отчета по почте
 */

echo CHtml::beginForm($this->mailPath);

echo CHtml::label('email', 'report_email_address');
echo CHtml::textField('email', '', array('id' => 'report_email_address'));
echo CHtml::hiddenField('id', $this->report->id);
?>
<br>
    <?= CHtml::checkBox('showContacts'); ?>
    Указать в письме контакты
    <br>
<?php 
if ( $this->allowSave )
{
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type'       => 'inverse',
        'size'       => 'large',
        'label'      => 'Отправить письмом',
        'htmlOptions' => array(
            'id' => 'send_report_mail',
        ),
    ));
}

echo CHtml::endForm();