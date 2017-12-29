<?php

namespace app\m0r1\widgets;

use Yii;

use yii\base\Widget;
use yii\helpers\Html;

use app\modules\image\models\Thumbnail;
use app\modules\image\models\ThumbnailSize;

class ThumbnailWidget extends Widget
{
    public $img;
    public $sizeid;
    public $viewFile = '@app/m0r1/views/propImageThumb.php';
    public $notfoundimg = 'https://placeholdit.imgix.net/~text?txtsize=10&txt=Image+not+found&w=80&h=80';
    
    public function run()
    {
	
	if ( is_null( $this->img ) )
	    return Html::img( $this->notfoundimg, [] );
	
	$size = ThumbnailSize::find()->where([ 'id' => $this->sizeid ])->one();
	
	if( is_null( $size ) )
	    return '';
	
	$thumb = Thumbnail::getImageThumbnailBySize($this->img,$size);
	
	if( is_null( $thumb ) )
	    return '';
	
	return $this->render( $this->viewFile, [ 'thumb' => $thumb ] );
    }
}