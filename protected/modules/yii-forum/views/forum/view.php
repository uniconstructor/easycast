<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links'=>$forum->getBreadcrumbs(),
));

$this->renderPartial('_subforums', array(
    'inforum'=>true,
    'forum' => $forum,
    'subforums' => $subforumsProvider,
));

$newthread = $forum->is_locked? '' : '<div class="newthread" style="">'.
        CHtml::link('Новая тема', 
                        array('/forum/thread/create', 'id'=>$forum->id),
                        array('class' => 'btn btn-success btn-mini pull-right')) .'</div>';

$gridColumns = array(
    array(
        'name' => 'Thread / Author',
        'headerHtmlOptions' => array('colspan' => '2'),
        'type' => 'html',
        'header' => 'Тема / Автор',
        'value' => '$data->is_locked ? '."'<i class=\"icon-lock\" style=\"font-size: 18pt;\"></i>' : '<i class=\"icon-file-alt\" style=\"font-size: 18pt;\"></i>'",
        'htmlOptions' => array('style' => 'width:20px;vertical-align:middle;'),
    ),
    array(
        'name' => 'subject',
        'headerHtmlOptions' => array('style' => 'display:none'),
        'type' => 'html',
        'value' =>'$data->renderSubjectCell()',
    ),
    array(
        'name' => 'postCount',
        'header' => 'Сообщения',
        'headerHtmlOptions' => array('style' => 'text-align:center;'),
        'htmlOptions' => array('style' => 'width:65px; text-align:center;'),
    ),
    array(
        'name' => 'view_count',
        'header' => 'Просмотры',
        'headerHtmlOptions' => array('style' => 'text-align:center;'),
        'htmlOptions' => array('style' => 'width:65px; text-align:center;'),
    ),
    array(
        'name' => 'Last post',
        'header' => 'Последнее сообщение',
        'headerHtmlOptions' => array('style' => 'text-align:center;'),
        'type' => 'html',
        'value' => '$data->renderLastpostCell()',
        'htmlOptions' => array('style' => 'width:200px; text-align:right;'),
    ),
);

// For admins, add column to delete and lock/unlock threads
$isAdmin = !Yii::app()->user->isGuest && Yii::app()->user->isAdmin;
if($isAdmin)
{
    // Admin links to show in extra column
    $deleteConfirm = "Вы уверены? Все сообщения будут также удалены!";
    $gridColumns[] = array(
        'class'=>'TbButtonColumn',
        'header'=>'Admin',
        'template'=>'{delete} {update}',
        'deleteConfirmation'=>"js:'".$deleteConfirm."'",
        'afterDelete'=>'function(){document.location.reload(true);}',
        'buttons'=>array(
            'delete'=>array('url'=>'Yii::app()->createUrl("/forum/thread/delete", array("id"=>$data->id))'),
            'update'=>array('url'=>'Yii::app()->createUrl("/forum/thread/update", array("id"=>$data->id))'),
        ),
        'htmlOptions' => array('style' => 'width:40px;text-align:center;'),
    );
}

// forum threads
$this->widget('forum.extensions.groupgridview.GroupGridView', array(
    'enableSorting' => false,
    'selectableRows' => 0,
    // 'emptyText'=>'', // No threads? Show nothing
    // 'showTableOnEmpty'=>false,
    'preHeader' => $newthread.CHtml::encode($forum->title),
    'preHeaderHtmlOptions' => array(
        'class' => 'preheader',
    ),
    'dataProvider' => $threadsProvider,
    'template'=> '{summary}' .'{pager}{items}{pager}',
    'extraRowColumns' => array('is_sticky'),
    'extraRowExpression' => '"<b>".($data->is_sticky?"Прикрепленные темы":"Темы")."</b>"',
    'columns' => $gridColumns,
    'htmlOptions'=>array(
        'class'=>Yii::app()->controller->module->forumTableClass,
    )
));
