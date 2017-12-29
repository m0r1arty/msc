<?php

namespace app\m0r1\assets;

use Yii;

use yii\web\AssetBundle;

class M0r1ImportExportAsset extends AssetBundle
{
    public $sourcePath = '@app/m0r1/assets';
    public $css = [
    ];
    public $js = [
	'js/jquery.cookie.js',
	'js/m0r1importexport.js',
    ];
    
    public $depends = [
	'yii\web\YiiAsset',
	'yii\bootstrap\BootstrapAsset',
    ];
}