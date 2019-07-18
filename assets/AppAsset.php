<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css'
    ];
    public $js = [
        "js/libs/jquery-1.10.2.min.js",
        "js/libs/modernizr-custom.js",
        "js/libs/jquery.magnific-popup.min.js",
        "js/libs/jquery.lazyload.min.js",
        "js/libs/selectbox-min.js",
        "js/bdq_validator.js",
        "js/bdp_common.js",
        "js/bdp_basic.js",
        "js/bdp_app.js",
        "js/libs/jquery.bubble-slider.js",
        "js/libs/jquery.mCustomScrollbar.concat.min.js",
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
