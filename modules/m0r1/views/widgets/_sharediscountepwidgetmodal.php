<?php

use yii\bootstrap\Modal;

use kartik\widgets\DateTimePicker;

$modal = new Modal([
	'header' => Yii::t('m0r1','Share Discount Edit Modal Header'),
	'size' => Modal::SIZE_LARGE,
]
);

$id = $modal->getId();

$lbls =[
    'close' => Yii::t('app','Close'),
    'edit' => Yii::t('app','Edit'),
    'enabled' => $model->getAttributeLabel('enabled'),
    'On' => Yii::t('m0r1','On'),
    'Off' => Yii::t('m0r1','Off'),
    'till' => $model->getAttributeLabel('till'),
    'save' => Yii::t('app','Save'),
    'assigns' => Yii::t('m0r1','M0r1 Share Discount Assigned Products'),
    'add' => Yii::t('app','Add'),
    'existsassigns' => Yii::t('m0r1','M0r1 Share Discount Exists Assigns'),
    'pid' => Yii::t('m0r1','PID'),
    'pidenter' => Yii::t('m0r1','Please PID enter'),
    'id' => Yii::t('m0r1','ID'),
    'cost' => Yii::t('m0r1','cost'),
    'name' => Yii::t('m0r1','name'),
    'cantdeletebecause' => Yii::t('m0r1','M0r1 Share Discount Cant Delete Product Link Because'),
    'cantaddbecause' => Yii::t('m0r1','M0r1 Share Discount Cant Create New Product Link Because'),
    'badpid' => Yii::t('m0r1','M0r1 Share Discount BAD PID'),
];

$ids = [
    'infodiv' => 'm0r1'.$id.'div',
    'items' => 'm0r1items'.$id.'div',
    'row' => 'm0r1'.$id.'row',
    'pid' => 'm0r1'.$id.'pid',
];

$widget = \kartik\widgets\SwitchInput::widget([
    'name' => 'enabled_'.$id,
    'pluginOptions' => [
	'size' => 'large',
        'onColor' => 'success',
        'offColor' => 'danger',
        'onText' => $lbls['On'],
        'offText' => $lbls['Off'],
    ]
]);

$widgets = [
    'enabled' => $widget,
];

$widget = \kartik\widgets\DateTimePicker::widget([
    'name' => 'till_'.$id,
    'pluginOptions' => [
	'todayHighlight' => true
    ]
]); 

$widgets['till'] = $widget;

echo <<<END
<div id="{$ids['infodiv']}">
</div>

<div class="panel panel-default">
  <div class="panel-heading">{$lbls['edit']}</div>
    <div class="panel-body">
        <div style="margin-left:20px;margin-right:20px;">
    	    <div class="row" style="text-align:left;">
    		<div class="col-sm-4">{$lbls['enabled']}</div>
    		<div class="col-sm-4">{$lbls['till']}</div>
    	    </div>
    	    <div class="row">
    		<div class="col-sm-4">{$widgets['enabled']}</div>
    		<div class="col-sm-4">{$widgets['till']}</div>
    		<div class="col-sm-4"><button id="m0r1shdisc{$id}" type="button" value="save" class="btn btn-primary"><i class="fa fa-save" style="margin-right:10px;"></i>{$lbls['save']}</div>
    	    </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">{$lbls['assigns']}</div>
    <div class="panel-body">
	
	<div class="panel panel-info">
	    <div class="panel-heading">{$lbls['add']}</div>
	    <div class="panel-body">
		<div class="row">
		    <div class="col-sm-6">
			<div class="input-group">
			    <span class="input-group-addon">{$lbls['pid']}</span><input type="text" id="{$ids['pid']}" class="form-control" placeholder="{$lbls['pidenter']}" />
			</div>
		    </div>
		    <div class="col-sm-4">
			<button type="button" value="save" onclick="return m0r1AddNewPlink();" class="btn btn-primary"><i class="fa fa-save" style="margin-right:10px;"></i>{$lbls['save']}</button>
		    </div>
		</div>
	    </div>
	</div>
	<div class="panel panel-default">
	    <div class="panel-heading">{$lbls['existsassigns']}</div>
	    <div class="panel-body" id="{$ids['items']}">
		
	    </div>
	</div>
    </div>
</div>

END;

$this->registerJs(<<<JS

$(document).ready(function()
{
    $('#m0r1shdisc{$id}').click(function()
    {
	if( parseInt( $('#{$id}').attr('data-edit') ) > 0 )
	{
	    var data = {};
	    
	    if ( $('input[name="enabled_{$id}"]').is(":checked") )
	    {
		data.enabled = 1;
	    }else{
		data.enabled = 0;
	    }
	    
	    data.till = $('input[name="till_{$id}"]').val();
	    
	    $.post('/m0r1/ajax/savesharediscount/'+$('#{$id}').attr('data-id'),data,function(obj){
		
		if( obj.status !== undefined )
		{
		    if( obj.status === 0 )
		    {
			m0r1AddAlert('{$id}','danger',obj.reason);
		    }else{
			m0r1AddAlert('{$id}',obj.type,obj.reason);
		    }
		}
		
	    },'json');
	}
    });
});

function m0r1AddNewPlink()
{
    if( parseInt( $('#{$id}').attr('data-edit') ) > 0 )
    {
	var data = {};
	
	data.pid = parseInt( $('#{$ids['pid']}').val() );
	
	if( isNaN(data.pid) || data.pid === 0 )
	{
	    m0r1AddAlert('{$id}','danger','{$lbls['badpid']}');
	    return;
	}
	
	$.post('/m0r1/ajax/createplink/'+$('#{$id}').attr('data-id'),data,function(obj)
	{
	    if( obj.status !== undefined )
	    {
		if( obj.status === 0 )
		{
		    m0r1AddAlert('{$id}','danger',obj.reason);
		}else{
		    m0r1AddAlert('{$id}',obj.type,obj.reason);
		    
		    if ( $('#{$ids['items']} > table').size() > 0 )
		    {
			var item = m0r1RenderRowPlink(obj.product);
			
			$('#{$ids['items']} > table > tbody').append(item);
		    }else{
			m0r1RenderTablePlink( [obj.product] );
		    }
		}
	    }
	},'json');
    }else{
	m0r1AddAlert('{$id}','danger','{$lbls['cantaddbecause']}');
    }
}

function m0r1UnlinkShareProduct(pid)
{
    if( parseInt( $('#{$id}').attr('data-edit') ) > 0 )
    {
	var data = {};
	
	data.msid = parseInt($('#{$id}').attr('data-id'));
	data.pid  = parseInt(pid);
	
	$.post('/m0r1/ajax/punlink/'+data.msid,data,function(obj)
	{
	    var tbody,table;
	    
	    if( obj.status !== undefined )
	    {
		if( obj.status === 0 )
		{
		    m0r1AddAlert('{$id}','danger',obj.reason);
		}else{
		    m0r1AddAlert('{$id}',obj.type,obj.reason);
		    
		    tbody = $('tr#{$ids['row']}_'+obj.pid).parent();
		    table = $(tbody).parent();
		    
		    $('tr#{$ids['row']}_'+obj.pid).remove();
		    
		    if( $('tr',tbody).size() === 0 )
		    {
			table.remove();
		    }
		}
	    }
	    
	},'json');
	
    }else{
	m0r1AddAlert('{$id}','danger','{$lbls['cantdeletebecause']}');
    }
}

function m0r1AddAlert(id,type,txt)
{
    var html = '<div class="alert alert-'+type+' alert-dismissible" role="alert">';
    html += '<button type="button" class="close" data-dismiss="alert" aria-label="{$lbls['close']}"><span>&times;</span></button>';
    html += txt;
    html += '</div>';

    $('#m0r1'+id+'div').append(html);
}

function m0r1RenderRowPlink(p)
{
    var html = '';
    
    html += '<tr id="{$ids['row']}_'+p.id+'">';

	html += '<td>'+p.id+'</td>';
	html += '<td>'+p.price_f+'</td>';
	html += '<td>'+p.name + '</td>';
        html += '<td><a class="btn btn-danger btn-sm" onclick="return m0r1UnlinkShareProduct('+p.id+');"><i class="fa fa-trash-o"></i></a></td>';

    html += '</tr>';
    
    return html;
}

function m0r1RenderTablePlink(products)
{
    var i,p,c = 0;
    var html = '';
    
    html += '<table class="table table-hover">';
    html += '<thead>';
    
	html += '<tr>';
	html += '<td>{$lbls['id']}</td>';
	html += '<td>{$lbls['cost']}</td>';
	html += '<td>{$lbls['name']}</td>';
	html += '<td>&nbsp;</td>';
	html += '</tr>';
    
    html += '</thead><tbody>';
    
    for( i in products )
    {
	p = products[i];
	
	html += m0r1RenderRowPlink(p);

	c++;
    }
    
    html += '</tbody></table>';
    
    if( c > 0 )
    {
	$('#{$ids['items']}').html(html);
    }
}

function m0r1ShareDiscountShowModal(id)
{
    $('#{$modal->getId()}').modal('show');
    $('#{$modal->getId()}').attr('data-id',id);
    
    $.post('/m0r1/ajax/getsharediscount/'+id,{},function(obj)
    {
	var mainid;
	
	if ( obj.status !== undefined )
	{
	    
	    if( parseInt(obj.status) === 0 )
	    {
		m0r1AddAlert('{$modal->getId()}','danger',obj.reason);
		$('#{$modal->getId()}').attr('data-edit','0');
	    }else{
		$('#{$modal->getId()}').attr('data-edit','1');
		
		mainid = $('input[name="enabled_{$id}"]').attr('id');
		if( obj.enabled )
		{
		    $('#'+mainid).bootstrapSwitch('state',true);
		}else{
		    $('#'+mainid).bootstrapSwitch('state',false);
		}
		
		mainid = $('input[name="till_{$id}"]').attr('id');
		
		$('#'+mainid).val(obj.till);
		
		if( obj.products !== undefined )
		{
		    if( obj.products.length > 0 )
		    {
			m0r1RenderTablePlink(obj.products);
		    }

		}
	    }
	    
	}else{
	    $('#{$id}').attr('data-edit');
	}
    },'json');
}

JS
,\yii\web\View::POS_END);
?>


<?php
echo $modal->run();