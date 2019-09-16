<?php

namespace lakerLS\pencil;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PencilAsset extends AssetBundle
{
    public $sourcePath = '@lakerLS/pencil/assets';
    public $jsOptions = ['position' => View::POS_END];
    public $css = [
        'css/pencil.css',
    ];
    public $js = [
        '//ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.min.js',
        'js/pencil-text.js',
        'js/pencil-image.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
