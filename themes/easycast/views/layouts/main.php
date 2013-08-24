<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>
<?php // шапка страницы
    $this->widget('ext.ECMarkup.ECHeader.ECHeader', $this->ecHeaderOptions);
?>

<div class="container" id="page">
    <?php // Навигация (хлебные крошки) ?>
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

    <?php // Подвал страницы
        $this->widget('ext.ECMarkup.ECFooter.ECFooter');
    ?>
</div><!-- page -->
</body>
</html>