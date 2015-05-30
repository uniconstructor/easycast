<?php
/**
 * Разметка landing-страниц: общей главной, главная для участника, главная для заказчика
 * Структуру разметуи можно изменить через регионы cockpit
 */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<?php region('layout-landing-head', ['view' => &$this]); ?>
<?php region('layout-landing-body', ['view' => &$this, 'content' => $content]); ?>
</html>
<?php
$this->endPage();