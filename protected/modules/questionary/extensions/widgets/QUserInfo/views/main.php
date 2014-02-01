<?php
/**
 * Основная информация: внешность и другие параметры
 * @todo изменить верстку таким образом, чтобы все поля были одинаковой высоты
 */
/* @var $this QUserInfo */
/* @var $data array */

?>
<h3 class="lead ec-details-subheader"><?= QuestionaryModule::t('looks'); ?></h3>

<div>
    <div class="row-fluid">
        <div class="span6">
            <?= $data['age']; ?>
        </div>
        <div class="span6">
            <?= $data['playage']; ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?= $data['physiquetype']; ?>
        </div>
        <div class="span6">
            <?= $data['eyecolor']; ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?= $data['hairlength']; ?>
        </div>
        <div class="span6">
            <?= $data['haircolor']; ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?= $data['looktype']; ?>
        </div>
        <div class="span6">
            <?= $data['addchar']; ?>
        </div>
    </div>
</div>


<h3 class="lead ec-details-subheader"><?= QuestionaryModule::t('sizes'); ?></h3>

<?php 
$class  = 'span6';
$column = '';
if ( $this->questionary->gender == 'female' AND 
   ( $this->questionary->titsize OR Yii::app()->user->checkAccess('Admin') ) )
{
    $class  = 'span4';
    $column .= CHtml::openTag('div', array('class' => $class));
    $column .= $data['titsize'];
    $column .= CHtml::closeTag('div');
}
?>

<div>
    <div class="row-fluid">
        <div class="span6">
            <?= $data['height']; ?>
        </div>
        <div class="span6">
            <?= $data['weight']; ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <?= $data['chestsize']; ?>
        </div>
        <div class="span4">
            <?= $data['waistsize']; ?>
        </div>
        <div class="span4">
            <?= $data['hipsize']; ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="<?= $class; ?>">
            <?= $data['wearsize']; ?>
        </div>
        <div class="<?= $class; ?>">
            <?= $data['shoessize']; ?>
        </div>
        <?= $column; ?>
    </div>
</div>
