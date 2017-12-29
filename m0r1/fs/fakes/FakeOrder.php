<?php

namespace app\m0r1\fs\fakes;

use Yii;

use yii\base\Component;

class FakeOrder extends Component
{
    public $id = 0;
    
    public $total_price = 0.0;
    
    private $_pids = [];
    
    public function setPIDs( $pids = [] )
    {
	$this->_pids = $pids;
    }
}
