<?php

namespace app\m0r1\assets;

use Yii;

use yii\web\AssetBundle;

class M0r1BBWAsset extends AssetBundle
{
    public $sourcePath = '@app/m0r1/assets';
    public $css = [
	'css/css.css',
    ];
    public $js = [
	'js/m0r1bonusbuttonwidget.js',
    ];
    
    public $depends = [
	'yii\web\YiiAsset',
	'yii\bootstrap\BootstrapAsset',
    ];
}