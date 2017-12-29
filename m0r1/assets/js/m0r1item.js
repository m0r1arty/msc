var M0r1Item = function(tubeid)
{
    let self_ = this;
    this.tubeid = tubeid;
    this.modalid = '';
    
    this.events = [];
    
    this.on = function( obj, evt, fn )
    {
	var i,found;
	
	found = false;
	
	obj = obj + '|' + evt;
	
	for( i in this.events )
	{
	    if( this.events[i].name === obj )
	    {
		this.events[i].fncs.push( fn );
		
		found = true;
		break;
	    }
	}
	
	if( found === false )
	{
	    this.events.push( {'name': obj, 'fncs': [ fn ]  } );
	}
    }
    
    this.trigger = function( obj, evt )
    {
	var i,j;
	
	obj = obj + '|' + evt;
	
	for ( i in this.events )
	{
	    if( this.events[i].name === obj )
	    {
		for( j in this.events[i].fncs )
		{
		    (this.events[i].fncs[j])();
		}
		break;
	    }
	}
    }
    
    this.setModalId = function(id)
    {
	this.modalid = id;
    }
    
    this.reorderCond = function( obj, el )
    {
	obj.action = 'reorderCondition';
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    var html = '', i;
	    
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    $("div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget[data-id='"+obj.id+"'][data-pos='"+obj.pos+"']").parent().remove();
		    
		    html += '<div>';
		    html += obj.view;
		    html += '</div>';
		    
		    if( obj.dir === 'down' )
		    {
			$(html).insertAfter(el);
		    }else{
			$(html).insertBefore(el);
		    }
		    
		    i = 0;
		    
		    $("div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget[data-id='"+obj.id+"']").each(function()
		    {
			$(this).attr('data-pos',i);
			i++;
		    });
		}
		
	    }catch(err)
	    {
		console.log(err);
	    }
	    
	},'json');
    }
    
    this.reorderItem = function( obj, el )
    {
	obj.action = 'reorderItem';
	obj.tubeid = this.tubeid;
	
	$.post('/m0r1/backend-fs/tube/ajax',obj,function(obj)
	{
	    var html = '',i;
	    
	    try
	    {
		
		html += '<div class="jarviswidget" draggable="true" data-id="'+obj.id+'" data-pos="'+obj.pos+'">';
		html += $("div.jarviswidget[data-id='"+obj.id+"']").html();
		html += '</div>';
		
		$("div.jarviswidget[data-id='"+obj.id+"']").remove();
		
		if( obj.dir === 'down' )
		{
		    $(html).insertAfter(el);
		}else{
		    $(html).insertBefore(el);
		}

		    i = 0;
		    
		    $('div.m0r1itemstube > div[data-id]').each(function()
		    {
			$(this).attr('data-pos',i);
			i++;
		    });

	    }catch(err)
	    {
		console.log(err);
	    }
	},'json');
    }
    
    this.insertItem = function ( obj , pos, el )
    {
	obj.action = 'insertItem';
	obj.pos = pos;
	obj.tubeid = this.tubeid;
	
	$.post('/m0r1/backend-fs/tube/ajax',obj,function(obj)
	{
		var html = '',i;
		
		try
		{

		if( obj.success !== undefined )
		{
		    html += '<div class="jarviswidget" draggable="true" data-id="'+obj.id+'" data-pos="'+obj.pos+'">';
			html += '<header><a class="btn btn-danger btn-sm m0r1itemdel"><i class="fa fa-trash-o"></i></a><h2><i class="fa fa-gear" style="margin-right:8px;"></i>'+obj.name+'</h2><div></div></header>';
			html += '<div>'+obj.view+'</div>';
			html += '<div class="m0r1conditions">';
			    html += '<div><a class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right:8px;"></i>'+obj.button+'</a></div>';
			html += '</div>';
		    html += '</div>';
		    
		    $(html).insertBefore(el);

		    i = 0;
		    
		    $('div.m0r1itemstube > div[data-id]').each(function()
		    {
			$(this).attr('data-pos',i);
			i++;
		    });
		    
		    self_.trigger(obj.ndname,'insert');
		}

		}catch(err)
		{
		}
	},'json');
    }
    
    this.confItem = function( obj )
    {
	obj.action = 'confItem';
	
	$.post('/m0r1/backend-fs/tube/ajax',obj,function(obj)
	{
	    
	    try
	    {
		
		self_.trigger(obj.ndname,'conf');
		
	    }catch(err)
	    {
		console.log(err);
	    }
	    
	},'json');
    }
    
    this.appendItem = function( obj )
    {
	obj.action = 'appendItem';
	obj.tubeid = this.tubeid;
	
	$.post('/m0r1/backend-fs/tube/ajax',obj,function(obj)
	{
	    var html = '';
	    
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    html += '<div draggable="true" class="jarviswidget" data-id="'+obj.id+'" data-pos="'+obj.pos+'">';
			html += '<header><a class="btn btn-danger btn-sm m0r1itemdel"><i class="fa fa-trash-o"></i></a><h2><i class="fa fa-gear" style="margin-right:8px;"></i>'+obj.name+'</h2><div></div></header>';
			html += '<div>'+obj.view+'</div>';
			html += '<div class="m0r1conditions">';
			    html += '<div><a class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right:8px;"></i>'+obj.button+'</a></div>';
			html += '</div>';
		    html += '</div>';
		    
		    $("div.m0r1itemstube").append(html);
		    
		    self_.trigger(obj.ndname,'append');
		}
		
	    }catch(err)
	    {
		//
	    }
	},'json');
    }
    
    this.delItem = function( obj )
    {
	obj.tubeid = this.tubeid;
	
	$.post( '/m0r1/backend-fs/tube/ajax',obj,function(obj)
	{
	    var i;
	    
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    $('div.jarviswidget[data-id="'+obj.id+'"]').remove();
		    
		    i = 0;
		    
		    $('div.m0r1itemstube > div[data-id]').each(function()
		    {
			$(this).attr('data-pos',i);
			i++;
		    });
		}
		
	    }catch(err)
	    {
	    }
	},'json');
    }
    
    this.dragStart = function(ev)
    {
	var oev = ev.originalEvent;
	var obj = {"from":"items","id":$(ev.currentTarget).attr('data-item-id')};
	
	oev.dataTransfer.setData("Text",JSON.stringify(obj));
	
	return true;
    }
    
    this.dragItemStart = function( ev )
    {
	var oev = ev.originalEvent;
	var obj = { "from":"item","id":$(ev.currentTarget).attr("data-id"), "pos" : parseInt( $(ev.currentTarget).attr("data-pos") ) };
	
	oev.dataTransfer.setData( "Text",JSON.stringify( obj ) );
	return true;
    }
    
    this.dragCondStart = function( ev )
    {
	var oev = ev.originalEvent;
	var obj = { "from": "cond", "id": $(ev.currentTarget).attr("data-id"), "pos" : parseInt( $(ev.currentTarget).attr("data-pos") ) };
	
	oev.dataTransfer.setData( "Text", JSON.stringify( obj ) );
	
	ev.stopPropagation();
	return true;
    }
    
    this.dragEnter = function(ev)
    {
	ev.preventDefault();
	return true;
    }
    
    this.dragOver = function(ev)
    {
	ev.preventDefault();
    }
    
    this.drop = function(ev)
    {
	var oev = ev.originalEvent;
	var obj;
	
	try
	{
	    
	    obj = JSON.parse( oev.dataTransfer.getData("Text") ) ;
	    
	    if( obj.from !== undefined )
	    {
		if( obj.from === 'items' )
		{
		    self_.appendItem(obj);
		}
	    }
	    
	}catch(err)
	{
	    console.log(err);
	}
	
	return false;
    }
    
    this.dropItem = function( evt )
    {
	var oev = evt.originalEvent;
	var obj,pos;
	var div = $(evt.currentTarget).parent().parent();
	
	div.removeClass('m0r1itemselected');

	evt.stopPropagation();
	
	
	try
	{
	    obj = JSON.parse( oev.dataTransfer.getData("Text") );
	    
	    if( obj.from !== undefined )
	    {
		if( obj.from === 'items' )
		{
		    self_.insertItem( obj,parseInt( $(evt.currentTarget).parent().parent().attr( 'data-pos' ) ), $(evt.currentTarget).parent().parent() );
		}else if( obj.from === 'item' )
		{
		    if( div.attr('data-id') != obj.id )
		    {
			pos = parseInt( div.attr('data-pos')  );
			obj.newpos = pos;
			
			if( obj.pos < pos )
			{
			    obj.direction = "down";
			}else{
			    obj.direction = "up";
			}
			
			self_.reorderItem( obj, div );
			
		    }
		}
	    }
	    
	}catch(err)
	{
	    console.log(err);
	}
    }
    
    this.dropCond = function( evt )
    {
	var oev = evt.originalEvent;
	var obj,pos;
	var div = $(evt.currentTarget).parent().parent();
	
	div.removeClass('m0r1itemselected');
	evt.stopPropagation();
	
	try
	{
	    
	    obj = JSON.parse( oev.dataTransfer.getData("Text") );
	    
	    obj.newpos = parseInt( div.attr('data-pos') );
	    
	    if ( obj.pos === obj.newpos )
		return;
	    
	    if( obj.from === 'cond' )
	    {
		
		if( obj.pos < obj.newpos )
		{
		    obj.direction = "down";
		}else{
		    obj.direction = "up";
		}
		self_.reorderCond(obj,div.parent());
	    }
	    
	}catch(err)
	{
	    console.log(err);
	}
    }
    
    this.delItemClick = function(evt)
    {
	var div = $(evt.currentTarget).parent().parent();
	var post = {};
	
	post.action = 'delItem';
	post.id = $(div).attr('data-id');
	post.pos = $(div).attr('data-pos');
	
	self_.delItem( post );
    }
    
    this.dragItemEnter = function(evt)
    {
	evt.preventDefault();
	
	$(evt.currentTarget).parent().parent().addClass('m0r1itemselected');
	
	return true;
    }
    
    this.dragItemOver = function(evt)
    {
	evt.preventDefault();
	$(evt.currentTarget).parent().parent().removeClass('m0r1itemselected');
    }

    this.dragItemLeave = function(evt)
    {
	evt.preventDefault();
	$(evt.currentTarget).parent().parent().removeClass('m0r1itemselected');
    }
    
    this.addCondBtnClick = function(evt)
    {
	var obj = {'action':'getConditions'};
	var div = $(evt.currentTarget).parent().parent().parent();
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    var el;
	    
	    if( obj.success !== undefined )
	    {
		$('div#'+self_.modalid + ' > div.modal-dialog > div.modal-content > div.modal-body').html( obj.view );
		$('select#select2-cond-modal').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		$('select#select2-cond-item-modal').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		
		$('div#'+self_.modalid).attr('data-tiid',div.attr('data-id'));
		$('div#'+self_.modalid).attr('data-action','add');
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').select2({});
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div > select').select2({});
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child > select').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div + div > a').append(obj.button);
		
		$('select#select2-cond-item-modal').trigger('select2:select');
		$('#'+self_.modalid).modal('show');
	    }
	},'json');
    }
    
    this.selectCond = function(evt)
    {
	var obj = {'action' : 'getCondItems', 'cond_id' : $("select#select2-cond-modal").val()};
	
	$('select#select2-cond-item-modal').html('');
	
	$('select#select2-cond-item-modal').select2({
	    placeholder: obj.placeholder,
	    minimumResultForSearch: -1
	});
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    $('select#select2-cond-item-modal').html(obj.view);
		    
		    $('select#select2-cond-item-modal').select2({
			placeholder: obj.placeholder,
			minimumResultForSearch: -1
		    });
		    
		    $('select#select2-cond-item-modal').trigger('select2:select');
		}
		
	    }catch(err)
	    {
		console.log(err);
	    }
	},'json');
    }
    
    this.selectCondItem = function( evt )
    {
	var t = $("option:selected",$("select#select2-cond-item-modal")).attr('data-type');
	
	$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child > select').val('==');
	$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child > select').select2();
	$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').val('ret');
	$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').select2();
	
	$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').trigger('select2:select');
	
	switch( t )
	{
	    case 'cond':
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val').hide();
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr').hide();
		
	    break;
	    
	    case 'valCond':
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val').show();
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr').hide();
		
	    break;
	    
	    case 'exprCond':
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val').hide();
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr').show();
		
	    break;
	}
    }
    
    this.selectCondAct = function( evt )
    {
	var cact = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').val();
	
	switch( cact )
	{
	    case 'ret':
	    case 'cont':
	    
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div').hide();
	    
	    break;
	    
	    case 'tube':
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div').show();
		
	    break;
	}
    }
    
    this.changeCondClick = function( evt )
    {
	var obj = {};
	var act = $('div#'+self_.modalid).attr('data-action');
	var dtype;
	
	if( act === 'add' )
	{
	    obj.action = 'addCondition';
	}else if( act === 'edit' ){
	    obj.action = 'changeCondition';
	    obj.pos = $('div#'+self_.modalid).attr('data-pos');
	}
	
	obj.id = $('div#'+self_.modalid).attr('data-tiid');
	obj.cond_id = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div > select').val();
	obj.cond_item = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div > select').val();
	
	dtype = $("option:selected",$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div > select')).attr('data-type');
	
	obj.type = dtype;
	
	switch( dtype )
	{
	    case 'valCond':
		
		obj.val = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val > input[type="text"]').val();
		
	    break;
	    
	    case 'exprCond':
		
		obj.expr = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child > select').val();
		obj.val = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child + div > input[type="text"]').val();
		
	    break;
	}
	
	obj.act = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').val();
	
	if( obj.act === 'tube' )
	{
	    obj.tubeid = $('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div > select').val();
	}
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    if( act === 'add' )
		    {
			$('div.m0r1itemstube > div[data-id="'+obj.id+'"] > div.m0r1conditions').append( '<div>' + obj.view + '</div>' );
		    }else{
			$('div.m0r1itemstube > div[data-id="'+obj.id+'"] > div.m0r1conditions > div > div.jarviswidget[data-id="'+obj.id+'"][data-pos="'+obj.pos+'"]').replaceWith( obj.view );
		    }
		    
		    $('div#'+self_.modalid).modal('hide');
		}
		
	    }catch(err)
	    {
	    }
	},'json');
    }
    
    this.editCondClick = function( evt )
    {
	var obj = {'action':'getCondition'};
	var div = $(evt.currentTarget).parent().parent().parent().parent().parent();
	var wdiv = $(evt.currentTarget).parent().parent();
	
	obj.id = wdiv.attr('data-id');
	obj.pos = wdiv.attr('data-pos');
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    var el;
	    
	    if( obj.success !== undefined )
	    {
		$('div#'+self_.modalid + ' > div.modal-dialog > div.modal-content > div.modal-body').html( obj.view );
		$('select#select2-cond-modal').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		$('select#select2-cond-item-modal').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		
		$('div#'+self_.modalid).attr('data-tiid',div.attr('data-id'));
		$('div#'+self_.modalid).attr('data-pos',wdiv.attr('data-pos'));
		$('div#'+self_.modalid).attr('data-action','edit');
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select').select2({});
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div > select').select2({});
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr > div:first-child > select').select2({
		    placeholder: obj.placeholder,
		    minimumResultForSearch: -1
		});
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div + div > a').append(obj.button);
		
		$('div#'+self_.modalid+' > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div + div > a > i').removeClass('fa-plus').addClass('fa-pencil');
		
		$('#'+self_.modalid).modal('show');
	    }
	},'json');
    }
    
    this.delCondClick = function( evt )
    {
	var obj = {};
	
	obj.action = 'delCondition';
	obj.id = $(evt.currentTarget).parent().parent().attr("data-id");
	obj.pos = $(evt.currentTarget).parent().parent().attr("data-pos");
	
	$.post('/m0r1/backend-tube/ajax',obj,function(obj)
	{
	    var i;
	    
	    try
	    {
		
		if( obj.success !== undefined )
		{
		    $('div.m0r1conditions > div:not(:first-child) > div.jarviswidget[data-id="'+obj.id+'"][data-pos="'+obj.pos+'"]').parent().remove();
		    
		    i = 0;
		    
		    $('div.m0r1conditions > div:not(:first-child) > div.jarviswidget').each(function(){
			$(this).attr('data-pos',i);
			i++;
		    });
		}
		
	    }catch(err)
	    {
	    }
	},'json');
    }
    
    $(document).on('dragstart',"div.m0r1item.m0r1trueitem",this.dragStart);
    $(document).on('dragstart',"div.m0r1itemstube > div.jarviswidget",this.dragItemStart);
    $(document).on('dragstart',"div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget",this.dragCondStart);
    $(document).on('dragenter',"div.m0r1itemstube",this.dragEnter);
    $(document).on('dragover',"div.m0r1itemstube",this.dragOver);
    $(document).on('drop',"div.m0r1itemstube",this.drop);
    $(document).on('click','a.m0r1itemdel',this.delItemClick);
    $(document).on('dragenter','div.m0r1itemstube > div.jarviswidget > header > a + h2 + div',this.dragItemEnter);
    $(document).on('dragenter',"div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget > header > a + h2 + div",this.dragItemEnter);
    $(document).on('drop','div.m0r1itemstube > div.jarviswidget > header > a + h2 + div',this.dropItem);
    //$(document).on('dragover','div.m0r1itemstube > div.jarviswidget',this.dragItemOver);
    $(document).on('dragleave','div.m0r1itemstube > div.jarviswidget > header > a + h2 + div',this.dragItemLeave);
    $(document).on('dragleave',"div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget > header > a + h2 + div",this.dragItemLeave);
    $(document).on('drop',"div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:not(:first-child) > div.jarviswidget > header > a + h2 + div",this.dropCond);
    $(document).on('click','div.m0r1itemstube > div.jarviswidget > div.m0r1conditions > div:first-child > a',this.addCondBtnClick);
    $(document).on('select2:select','select#select2-cond-modal',this.selectCond);
    $(document).on('select2:select','select#select2-cond-item-modal',this.selectCondItem);
    $(document).on('select2:select','div > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child > select',this.selectCondAct);
    $(document).on('click','div > div.modal-dialog > div.modal-content > div.modal-body > div + div + div.m0r1-select-cond-val + div.m0r1-select-cond-expr + div.m0r1-select-cond-act > div:first-child + div + div > a',this.changeCondClick);
    $(document).on('click','div.m0r1conditions > div:not(:first-child) > div.jarviswidget > header > a:first-child + a',this.delCondClick);
    $(document).on('click','div.m0r1conditions > div:not(:first-child) > div.jarviswidget > header > a:first-child',this.editCondClick);
}

