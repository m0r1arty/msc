<?php

namespace app\m0r1\fs;

interface StrategyInterface
{
    
    public function init();
    public function run( $order, $tube_id );
    
    public function getPBundle();
    public function getTBundle();
}
