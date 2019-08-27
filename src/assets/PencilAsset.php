<?php

namespace lakerLS\pencil\assets;

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
        'js/pencil.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\web\YiiAsset',
    ];
}
