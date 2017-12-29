<?php

namespace app\m0r1\fs;

class M0r1FSThingBundle extends AbstractM0r1FSBundle
{
    public function getItemByType( $type )
    {
	foreach( $this->_items as $item )
	{
	    if( $item->type === $type )
		return $item;
	}
	
	return NULL;
    }
}