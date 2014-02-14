<?php 
/**
 * Основной файл разметки сайта
 */
/* @var $this Controller */
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/helpers.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/yii.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
	<script src="<?php echo Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script>
</head>
<body class="ec-body">
    <?php // шапка страницы
    $ecHeaderOptions = array();
    if ( isset($this->ecHeaderOptions) )
    {
        $ecHeaderOptions = $this->ecHeaderOptions;
    }
    $this->widget('ext.ECMarkup.ECHeader.ECHeader', $ecHeaderOptions);
    ?>
    <div class="container" id="page"><!-- page -->
        <?php // Верхняя навигация (хлебные крошки) 
        if ( isset($this->breadcrumbs) )
        {
    	    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    			'links'       => $this->breadcrumbs,
                'htmlOptions' => array('class' => 'breadcrumb span12 ec-breadcrumb'),
    		)); 
        }
    	?>
    	<!-- breadcrumbs -->
    	<?php // основное содержимое страницы 
	    echo $content;
	    ?>
    	<div class="clear"></div>
        <?php // Подвал страницы
        $this->widget('ext.ECMarkup.ECFooter.ECFooter');
        ?>
    </div><!--/ page -->
</body>
</html>