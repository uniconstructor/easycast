<?php
/**
 * Форма отправки отчета по почте
 */

echo CHtml::beginForm($this->mailPath);
// id отчета, отправляемого по почте
echo CHtml::hiddenField('id', $this->report->id);
echo CHtml::label('email', 'report_email_address');
echo CHtml::textField('email', '', array('id' => 'report_email_address'));

?>
<br>
    <?= CHtml::checkBox('showContacts'); ?>
    <b title="Почему бы и нет? :)">Указать в письме контакты</b>
    <br><br>
<?php 
if ( $this->allowSendMail )
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