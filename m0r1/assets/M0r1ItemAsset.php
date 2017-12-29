<?php

namespace app\m0r1\assets;

use Yii;

use yii\web\AssetBundle;

class M0r1ItemAsset extends AssetBundle
{
    public $sourcePath = '@app/m0r1/assets';
    public $css = ['css/m0r1item.css'];
    public $js = ['js/m0r1item.js'];
    
    public $depends = ['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset'];
}