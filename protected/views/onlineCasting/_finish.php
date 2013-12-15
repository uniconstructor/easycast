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
        
        /*$castingAttributes = array();
        foreach ( $onlineCastingForm->attributes as $name => $value )
        {
            $castingAttributes[] = array(
                'name'  => $name,
                'label' => $onlineCastingForm->getAttributeLabel($name),
                'type'  => 'raw',
            );
        }*/
        ?>
        <h3>Информация о кастинге</h3>
        <?php
        // информация о кастинге
        $castingData = $onlineCastingForm->attributes;
        // пишем тип проекта словами
        $castingData['projecttype'] = Project::model()->gettypetext($castingData['projecttype']);
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $castingData,
            'attributes' => array(
                array(
                    'name'  => 'projectname',
                    'label' => $onlineCastingForm->getAttributeLabel('projectname'),
                ),
                array(
                    'name'  => 'projecttype',
                    'label' => $onlineCastingForm->getAttributeLabel('projecttype'),
                ),
                array(
                    'name'  => 'projectdescription',
                    'label' => 'Описание проекта',//$onlineCastingForm->getAttributeLabel('projectdescription'),
                    'type'  => 'html',
                ),
                array(
                    'name'  => 'plandate',
                    'label' => 'Предполагаемая дата',//$onlineCastingForm->getAttributeLabel('plandate'),
                ),
                array(
                    'name'  => 'eventdescription',
                    'label' => 'О съемках',//$onlineCastingForm->getAttributeLabel('eventdescription'),
                    'type'  => 'html',
                ),
            ),
        ));
        ?>
        <h3>Требования к участникам</h3>
        <?php 
        // информация о роли
        $roleData = $onlineCastingRoleForm->attributes;
        if ( $roleData['salary'] )
        {
            $roleData['salary'] .= 'р.';
        }
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $roleData,
            'attributes' => array(
                array(
                    'name'  => 'name',
                    'label' => $onlineCastingRoleForm->getAttributeLabel('name'),
                ),
                array(
                    'name'  => 'description',
                    'label' => $onlineCastingRoleForm->getAttributeLabel('description'),
                    'type'  => 'html',
                ),
                array(
                    'name'  => 'salary',
                    'label' => $onlineCastingRoleForm->getAttributeLabel('salary'),
                ),
            ),
        ));
        ?>
        <h3>Ваши контактные данные</h3>
        <?php
        // ваши контактные данные
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $onlineCastingForm->attributes,
            'attributes' => array(
                array(
                    'name'  => 'name',
                    'label' => $onlineCastingForm->getAttributeLabel('name'),
                ),
                array(
                    'name'  => 'lastname',
                    'label' => $onlineCastingForm->getAttributeLabel('lastname'),
                ),
                array(
                    'name'  => 'email',
                    'label' => $onlineCastingForm->getAttributeLabel('email'),
                    'type'  => 'html',
                ),
                array(
                    'name'  => 'phone',
                    'label' => $onlineCastingForm->getAttributeLabel('phone'),
                ),
            ),
        ));
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
        // назад
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'default',
            'size'       => 'large',
            'label'      => '< Назад',
            'url'        => Yii::app()->createUrl('/onlineCasting/create', array('step' => 'roles')),
        ));
        echo '&nbsp;';
        // кнопка создания кастинга
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Создать кастинг >',
            'url'        => Yii::app()->createUrl('/onlineCasting/conclusion'),
        ));
        ?>
    </div>
</div>
