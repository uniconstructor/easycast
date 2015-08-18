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
    public $baseUrl  = '@web';
    public $css = [
        // @todo судя по всему стандартные стили bootstrap подгружаются автоматически:
        //       убрать отсюда эти строки если они не пригодятся
        //'css/bootstrap.min.css',
        'css/custom/global.css',
        // @todo установка через npm/bower
        'css/custom/jquery.fancybox.css',
        'css/custom/jquery.fancybox-thumbs.css',
        // @todo установка через npm/bower
        'css/custom/icheck/minimal/minimal.css',
        // @todo загружать библиотеки локально вместо CDN
        // @todo установка через npm/bower
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/css/swiper.min.css',
    ];
    public $js = [
        'js/custom/require.js',
        // @todo загружать библиотеки локально вместо CDN
        // @todo установка через npm/bower
        'https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js',
        'https://oss.maxcdn.com/respond/1.4.2/respond.min.js',
        // @todo установка через npm/bower
        'js/custom/icheck.min.js',
        // @todo установка через npm/bower
        'js/custom/imagesloaded.pkgd.min.js',
        // @todo установка через npm/bower (и что это за пакет вообще, кстати?)
        'js/custom/positions.js',
        // @todo вынести скрипты из script.js в регионы виджетов
        'js/custom/script.js',
        // @todo загружать библиотеки локально вместо CDN
        // @todo установка через npm/bower
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.jquery.min.js',
        // @todo установка через npm/bower
        'js/custom/jquery.fancybox.pack.js',
        'js/custom/jquery.fancybox-thumbs.js',
        
        // @todo решить что делать со скриптами соединительных точек
        //'js/custom/dots/TweenLite.min.js',
        //'js/custom/dots/EasePack.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
