<?php
// For admins, add link to delete post
$isAdmin = !Yii::app()->user->isGuest && Yii::app()->user->isAdmin;
?>

<div class="post">
    <div class="header" style="padding: 5px; background-color: #333333; margin-top: 2px;">
        <?php echo Yii::app()->controller->module->format_date($data->created, 'long'); ?> by <?php echo CHtml::link(CHtml::encode($data->author->name), $data->author->url); ?>
        <?php if($data->editor) echo ' (Отредактировано: '. Yii::app()->controller->module->format_date($data->updated, 'long') .' '. CHtml::link(CHtml::encode($data->editor->name), $data->editor->url) .')'; ?>
        <?php
            if($isAdmin)
            {
                $deleteConfirm = "Вы уверены? Этот пост будет удален!";
                echo '<div class="admin" style="float:right; border:none;">'.
                        CHtml::ajaxLink(Yii::t('coreMessages', 'delete'),
                            array('/forum/admin/deletepost', 'id'=>$data->id),
                            array('type'=>'POST', 'success'=>'function(){document.location.reload(true);}'),
                            array('confirm'=>$deleteConfirm, 'id'=>'post'.$data->id, 'class' => 'btn btn-danger btn-mini')
                        ).
                     '</div>';
            }
        ?>
    </div>
    <div class="content well" style="margin-bottom: 0px;">
        <?php
            $this->beginWidget('CMarkdown', array('purifyOutput'=>true));
                echo $data->content;
            $this->endWidget();

            if($data->author->signature)
            {
                echo '<br />---<br />';
                $this->beginWidget('CMarkdown', array('purifyOutput'=>true));
                    echo $data->author->signature;
                $this->endWidget();
            }
        ?>
    </div>
    <?php if($isAdmin || Yii::app()->user->id == $data->author_id): ?>
        <div class="footer" style="height: 35px; text-align: right;">
            <?php echo CHtml::link( '<i class="icon-edit"></i> '.Yii::t('coreMessages', 'edit'), 
                    array('/forum/post/update', 'id'=>$data->id), 
                    array('class' => 'pull-right btn btn-info')); ?>
        </div>
    <?php endif; ?>
</div>
