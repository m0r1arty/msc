<?php

namespace app\m0r1\fs;

interface M0r1FSDiscountInterface
{
    public function checkM0r1Discount($discount, $product = NULL, $order = NULL);
}