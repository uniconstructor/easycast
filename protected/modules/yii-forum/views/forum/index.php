<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links' => array('Форум')
));

if(!Yii::app()->user->isGuest && Yii::app()->user->isAdmin)
{
    echo '<div style="text-align:right;">&nbsp;'. 
        CHtml::link('Новый форум', array('/forum/forum/create'), array('class' => 'pull-right btn btn-success')) .
        '</div><br />';
}

foreach($categories as $category)
{
    $this->renderpartial('_subforums', array(
        'forum'=>$category,
        'subforums'=>new CActiveDataProvider('Forum', array(
            'criteria'=>array(
                'scopes'=>array('forums'=>array($category->id)),
            ),
            'pagination'=>false,
        )),
    ));
}