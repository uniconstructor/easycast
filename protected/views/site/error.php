<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Ошибка';
$this->breadcrumbs=array(
	'Ошибка',
);
?>

<h2>Ошибка <?php echo $code; ?></h2>

<div class="error">
<?php 
if ( defined('YII_DEBUG') AND YII_DEBUG === true )
{
    echo CHtml::encode($message);
}
?>
</div>