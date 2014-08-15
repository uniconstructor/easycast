<?php
/**
 * Список возможных разделов для заявки участника
 */
/* @var $this SmartMemberInfo */
?>
<div class="col-md-10">
    <?php
    // получаем разделы в которых лежит эта заявка
    $sections = $this->getVacancySections($projectMember);
    $options  = array(
        'class' => 'checkbox style-0',
    );
    foreach ( $sections as $id => $section )
    {
        $options['id']           = 'member-'.$projectMember->id.'-'.$id;
        echo '<div class="checkbox"><label>';
        // полядок всегда одинаковый: [id заявки][id ссылки на раздел]
        echo CHtml::checkBox('member['.$projectMember->id.']['.$id.']', 
            $section['checked'], $options);
        echo '<span>'.$section['name'].'</span>';
        echo '</label></div>';
        // значение в базе меняется по нажатию галочки
        $submitAjax = CHtml::ajax(array(
            'dataType' => 'json',
            'type'     => 'post',
            //'success'  => '',
            'data'     => array(
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                'sectionInstanceId' => $id,
                'memberId'          => $projectMember->id,
                'newValue'          => "js:function () { $.jGrowl('[Данные сохранены]');return $('#member-{$projectMember->id}-{$id}').is(':checked');}",
            ),
            'url' => Yii::app()->createUrl('//admin/ajax/changeMemberCategory'),
        ));
        echo "<script>$('#member-{$projectMember->id}-{$id}').bind('change', function (e) {
            {$submitAjax}
        });</script>";
    }
?>
</div>