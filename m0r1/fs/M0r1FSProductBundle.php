<?php

namespace app\m0r1\fs;

class M0r1FSProductBundle extends AbstractM0r1FSBundle
{
    public function getProducts()
    {
	return $this->_items;
    }
}