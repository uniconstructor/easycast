<?php

$this->widget('bootstrap.widgets.TbBreadcrumbs', array('links'=>$thread->getBreadcrumbs()));

$header = '<div class="preheader"><div class="preheaderinner"><h3>'. CHtml::encode($thread->subject) .'</h3></div></div>';
$footer = $thread->is_locked? '':'<div class="footer">'. 
                CHtml::link('Ответить', 
                                array('/forum/thread/newreply', 'id'=>$thread->id),
                                array('class' => 'btn btn-success pull-right')) .
                '</div>';
?>

<?php
    $this->widget('bootstrap.widgets.TbListView', array(
        //'htmlOptions'=>array('class'=>'thread-view'),
        'dataProvider'=>$postsProvider,
        'template'=>'{summary}{pager}'. $header .'{items}{pager}'. $footer,
        'itemView'=>'_post',
        'itemsCssClass' => 'table table-bordered table-striped',
        'htmlOptions'=>array(
            'class'=>Yii::app()->controller->module->forumListviewClass,
        ),
    ));
