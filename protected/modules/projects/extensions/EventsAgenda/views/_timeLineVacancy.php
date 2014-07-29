<?php
/**
 * Отображение одной роли маленьким блоком в списке событий в режиме timeline
 */
/* @var $vacancy EventVacancy */
?>
<div class="row-fluid" style="background: rgba(0, 0, 0, 0.3);border-radius: 5px;margin-top:3px;">
    <div class="row-fluid text-center">
        <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'id'    => 'vacancy_description_'.$vacancy->id,
            'label' => CHtml::encode($vacancy->name),
            'type'  => 'default',
            'icon'  => 'icon-chevron-left',
            'htmlOptions' => array(
                'data-title'     => CHtml::encode($vacancy->name),
                'data-placement' => 'left',
                'data-content'   => '<div style="color:#000;font-size: 0.8em;">'.$vacancy->description.'</div>',
                'data-toggle'    => 'popover',
                'data-html'      => true,
                'class'          => 'btn-block'
            ),
        ));
        $tooltip = array(
            'title'     => 'Нажмите чтобы посмотреть подробную информацию',
            'trigger'   => 'hover',
            'placement' => 'top',
        );
        echo '<script>$("#vacancy_description_'.$vacancy->id.'").tooltip('.CJSON::encode($tooltip).');</script>';
        ?>
    </div>
    <div class="row-fluid text-center">
        <?php 

        if ( $vacancy->salary AND ! $vacancy->event->isExpired() AND
             $vacancy->status === EventVacancy::STATUS_ACTIVE AND 
             $this->userMode === 'user' AND 
           ( Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->checkAccess('User') ) )
        {
            echo '<span class="badge badge-success">Оплата: '.$vacancy->salary.' р.</span>';
        }
        if ( $vacancy->status === EventVacancy::STATUS_FINISHED OR 
             $vacancy->event->isExpired() )
        {
            echo '<span class="badge">Отбор завершен</span>';
        }
        ?>
    </div>
    <div class="row-fluid text-center">
        <?php 
        
        $this->widget('projects.extensions.VacancyActions.VacancyActions', array(
            'isAjaxRequest' => Yii::app()->request->isAjaxRequest,
            'questionaryId' => $this->questionary->id,
            'buttonSize'    => 'medium',
            'vacancy'       => $vacancy,
            'mode'          => 'normal',
            'buttonClass'   => 'btn-block',
        ));
        ?>
    </div>
</div>