<?php

$n_items = [];

for ( $i = 0; $i < ( ( ( count($items) % $perrow ) > 0 )? ( count($items) / $perrow ) : ( count($items) / $perrow ) ); $i++ )
{
    $n_items[] = array_slice( $items, $i * $perrow, $perrow );
}

foreach( $n_items as $items )
{
    ?>
    <div class="row">
	<?php
	    foreach( $items as $item )
	    {
		echo $this->render(
		    isset( $bonuses[ $item['id'] ] )? '@app/m0r1/widgets/views/ajaxitem_used' : '@app/m0r1/widgets/views/ajaxitem',['itemclasses'=>$itemclasses,'item'=>$item, 'bonuses' => $bonuses ]);
	    }
	?>
    </div>
    <?php
}