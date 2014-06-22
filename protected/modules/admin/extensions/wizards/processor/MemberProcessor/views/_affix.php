<?php
/**
 * Плавающий список всех разделов слева
 * @todo вывести количество заявок у каждой вкладки
 */
/* @var $this MemberProcessor */
?>
<ul class="nav nav-list nav-stacked dropdown dropdown-bootstrap-fix affix" style="background-color: #eee;">
    <?php 
    foreach ( $this->vacancy->catalogSectionInstances as $instance )
    {// отображаем все вкладки прикрепленные к этой роли
        $active = '';
        if ( $instance->id == $this->sectionInstanceId )
        {
            $active = 'active';
        }
        $url = Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions(array('siid' => $instance->id)));
        echo '<li class="'.$active.'"><a href="'.$url.'" style="text-transform:capitalize;font-weight:normal;padding:10px;"><i class="icon-chevron-right"></i>'.$instance->section->name.'</a></li>';
    }
    // внизу всегда добавляем вкладку с нераробранными
    $url = Yii::app()->createUrl($this->widgetRoute, $this->getLinkOptions(array('siid' => 0)));
    echo '<li><a href="'.$url.'" style="text-transform:capitalize;font-weight:normal;padding:10px;">
            <i class="icon-chevron-right"></i> Без категории&nbsp;<span class="badge badge-info pull-right">'.
            $this->getMemberCount($this->sectionInstanceId).'</span></a></li>';
    ?>
</ul>
