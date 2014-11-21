<?php
/**
 * Плавающий список всех разделов слева
 * @todo вывести количество заявок у каждой вкладки
 */
/* @var $this MemberProcessor */
?>
<ul class="nav nav-list nav-stacked dropdown dropdown-bootstrap-fix" style="background-color:#eee;max-width:210px;">
    <?php 
    // сверху всегда добавляем вкладку "все"
    $url = Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions(array('siid' => -1)));
    echo '<li><a href="'.$url.'" style="text-transform:capitalize;font-weight:normal;font-size:10px;padding:10px;">
            <i class="icon-chevron-right"></i>Все &nbsp;
            <span class="badge badge-info pull-right">'.$this->getMemberCount(-1).'</span></a></li>';
    foreach ( $this->vacancy->catalogSectionInstances as $instance )
    {/* @var $instance CatalogSectionInstance */
        // отображаем все вкладки прикрепленные к этой роли
        $active = '';
        if ( ! Yii::app()->user->checkAccess('Admin') AND ! $instance->visible )
        {// скрытые вкладки отображаются только админам
            continue;
        }
        if ( $instance->id == $this->sectionInstanceId )
        {// активная в данный момент вклажка помечается стандартным классвом из bootstrap
            $active = 'active';
        }
        $url = Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions(array('siid' => $instance->id)));
        echo '<li class="'.$active.'"><a href="'.$url.'" style="text-transform:capitalize;font-weight:normal;font-size:10px;padding:10px;">
            <i class="icon-chevron-right"></i>'.$instance->section->name.'&nbsp;
            <span class="badge badge-info pull-right">'.$this->getMemberCount($instance->id).'</span></a></li>';
    }
    if ( Yii::app()->user->checkAccess('Admin') )
    {// заявки без категории (видны только админам)
        $url = Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions(array('siid' => 0)));
        echo '<li><a href="'.$url.'" style="text-transform:capitalize;font-weight:normal;font-size:10px;padding:10px;">';
        echo '<i class="icon-chevron-right"></i> Без категории&nbsp;<span class="badge badge-info pull-right">';
        echo $this->getMemberCount(0).'</span></a></li>';
    }
    ?>
</ul>
