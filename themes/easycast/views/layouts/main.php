<!DOCTYPE html><?php /* @var $this Controller */ ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/yii.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php
	// @deprecated осталось от старой версии bootstrap
	// @todo удалить при рефакторинге 
    //Yii::app()->bootstrap->register();
    ?>
</head>
<body>
    <?php // шапка страницы
        $ecHeaderOptions = array();
        if ( isset($this->ecHeaderOptions) )
        {
            $ecHeaderOptions = $this->ecHeaderOptions;
        }
        $this->widget('ext.ECMarkup.ECHeader.ECHeader', $ecHeaderOptions);
    ?>
    <div class="container" id="page">
        <?php // Верхняя навигация (хлебные крошки) 
        if ( isset($this->breadcrumbs) )
        {
    	    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    			'links' => $this->breadcrumbs,
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
    </div><!-- page -->
</body>
</html>