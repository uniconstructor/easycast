<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
        //'css/bootstrap.min.css',
        'css/custom/global.css',
        'css/custom/jquery.fancybox.css',
        'css/custom/jquery.fancybox-thumbs.css',
        'css/custom/icheck/minimal/minimal.css',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/css/swiper.min.css',
    ];
    public $js = [
        'https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js',
        'https://oss.maxcdn.com/respond/1.4.2/respond.min.js',
        'js/custom/icheck.min.js',
        'js/custom/imagesloaded.pkgd.min.js',
        'js/custom/positions.js',
        'js/custom/script.js',
        'js/custom/dots/TweenLite.min.js',
        'js/custom/dots/EasePack.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.jquery.min.js',
        'js/custom/jquery.fancybox.pack.js',
        'js/custom/jquery.fancybox-thumbs.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
