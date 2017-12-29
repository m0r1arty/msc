<?php

namespace app\m0r1\assets;

use Yii;

use yii\web\AssetBundle;

class M0r1ItemDiscountAsset extends AssetBundle
{
    public $sourcePath = '@app/m0r1/assets';
    public $css = ['css/m0r1itemdiscount.css'];
    public $js = ['js/m0r1itemdiscount.js'];
    
    public $depends = ['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset','app\m0r1\assets\M0r1ItemAsset'];
}