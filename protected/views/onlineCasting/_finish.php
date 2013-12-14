<?php
/**
 * Страница завершения создания онлайн-кастинга, с информацией о проекте, мероприятии и роли
 */
/* @var $this OnlineCastingController */
/* @var $onlineCastingForm OnlineCastingForm */
/* @var $onlineCastingRoleForm OnlineCastingRoleForm */

?>
<div id="wizard-bar" class="progress progress-striped">
    <div class="bar"></div>
</div>
<div class="span8 offset2">
    <div class="alert alert-info">
        Для создания кастинга все готово.<br>
        Пожалуйста проверьте введенные вами данные еще раз, и если все верно - нажмите кнопку "создать".
    </div>
    <div class="row">
    <div class="span8">
        <?php 
        // информация о кастинге
        // @todo заставить работать виджет editable
        /*$this->widget(
            'bootstrap.widgets.TbEditableDetailView',
            array(
                'id'   => 'region-details',
                'data' => $onlineCastingForm,
                'url'  => '#',//$endpoint,
                'attributes' => array(
                    'name',
                )
            )
        );*/
        
        $castingAttributes = array();
        foreach ( $onlineCastingForm->attributes as $name => $value )
        {
            $castingAttributes[] = array(
                'name'  => $name,
                'label' => $onlineCastingForm->getAttributeLabel($name),
                'type'  => 'raw',
            );
        }
        $this->widget('bootstrap.widgets.TbDetailView', array(
                'data'       => $onlineCastingForm->attributes,
                'attributes' => $castingAttributes,
            )
        );
        ?>
    </div>
    <div class="span4">
        <?php 
        // информация о роли
        ?>
    </div>
    </div>
    <div class="row" style="text-align:center;">
        <?php 
        // кнопка создания кастинга
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Создать кастинг',
            'url'        => Yii::app()->createUrl('/onlineCasting/conclusion'),
        ));
        ?>
    </div>
</div>
