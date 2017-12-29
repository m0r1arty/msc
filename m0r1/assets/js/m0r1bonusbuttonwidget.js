var M0r1BBW = function(modalid,buttonid,orderid)
{
    let self_ = this;
    
    const ESC_DUMMY	= 0;
    const ESC_CHTOTAL 	= 1;

    this.modalid = modalid;
    this.buttonid = buttonid;
    this.page = 1;
    this.countPages = 1;
    this.orderid = orderid;
    
    this.alert = function(type,text)
    {
	var html = '';
	
	const counter = M0r1BBW.counter;
	
	html += '<div class="alert alert-'+type+'" id="'+M0r1BBW.autoIdPrefix+M0r1BBW.counter+'">';
	html += '<button type="button" class="close" data-dismiss="alert">x</button>';
	html += text;
	html += '</div>';
	
	$('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-flash-' + this.modalid).append(html);
	
	M0r1BBW.counter++;
	
	$('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-flash-'+this.modalid+' div.alert#'+M0r1BBW.autoIdPrefix+counter).fadeTo(2000,500).slideUp(500,function()
	{
	    $('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-flash-'+self_.modalid+' div.alert#'+M0r1BBW.autoIdPrefix+counter).slideUp(500,function(){
		$('div#' + self_.modalid + ' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-flash-'+self_.modalid+' div.alert#'+M0r1BBW.autoIdPrefix+counter).remove();
	    });
	});
    }
    
    this.chPage = function( pagenum )
    {
	var post = {'page': pagenum};
	
	if( self_.orderid !== undefined )
	    post.order_id = self_.orderid;
	
	$.post('/m0r1/ajax/getbonuspage',post,function(obj){
	    
	    self_.page = obj.page;
	    self_.countPages = obj.countPages;

	    $('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-body-'+self_.modalid).html(obj.bonuses);
	    $('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:first + div').html(obj.info);
	    
	    if ( self_.page == 1 )
	    {
		$('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:first a').addClass('disabled');
	    }else{
		$('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:first a').removeClass('disabled');
	    }
	    
	    if( self_.page == self_.countPages )
	    {
		$('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:last a').addClass('disabled');
	    }else{
		$('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:last a').removeClass('disabled');
	    }
	    
	},'json');
    }
    
    this.Shop = function( post )
    {
	if( self_.orderid !== undefined )
	    post.order_id = self_.orderid;

	$.post( '/m0r1/ajax/bshop', post, function(obj){
	    
	    let panel1, panel2;
	    
	    if( obj.error !== undefined )
	    {
		obj.error.forEach( function(err){
		    
		    self_.alert('danger',err.msg);
		
		    switch( err.subcode )
		    {
			case ESC_CHTOTAL:
			    
			    panel1 = $('div[data-id="'+err.id+'"]').children('div.m0r1bbw-count-panel');
			    panel2 = $('div[data-id="'+err.id+'"]').children('div.m0r1bbw-shop-panel');
			    
			    panel1.attr('data-total-count',err.total);
			    panel1.attr('data-count',err.total);
			    panel1.children('div.row').children('div.col-md-4:eq(1)').children('div:eq(0)').html(err.total);
			    
			break;
		    }
		    
		});
	    }else if( obj.success !== undefined )
	    {
		self_.alert( 'success',obj.success.msg );
		
		panel1 = $('div[data-id="'+obj.success.id+'"]').children('div.m0r1bbw-count-panel');
		panel2 = $('div[data-id="'+obj.success.id+'"]').children('div.m0r1bbw-shop-panel');
		
		panel2.children('div:eq(0)').hide();
		panel2.children('div:eq(1)').show();
		
		if( panel1.is('[data-count-backup]') )
		{
		    panel1.removeAttr('data-count-backup');
		}
		
		panel2.addClass('m0r1bbw-shop-panel-used');
		$('a#'+self_.buttonid).html(obj.button);
	    }
	    
	},'json');
    }
    
    this.UnShop = function( post )
    {
	if( self_.orderid !== undefined )
	    post.order_id = self_.orderid;

	$.post( '/m0r1/ajax/bunshop', post, function(obj){
	    let panel1,panel2;
	    
	    if( obj.warning !== undefined )
	    {
		obj.warning.forEach( function( warn ){
		    
		    self_.alert( 'warning', warn.msg );
		    
		} );
	    }
	    
	    if( obj.error !== undefined )
	    {
		obj.error.forEach( function( err )
		{
		    self_.alert( 'danger', err.msg );
		});
	    }
	    
	    if ( obj.error === undefined )
	    {
		panel1 = $('div[data-id="'+obj.id+'"]').children('div.m0r1bbw-count-panel');
		panel2 = $('div[data-id="'+obj.id+'"]').children('div.m0r1bbw-shop-panel');

		panel2.children('div:eq(0)').show();
		panel2.children('div:eq(1)').hide();
		
		panel2.removeClass('m0r1bbw-shop-panel-used');
		
		$('a#'+self_.buttonid).html(obj.button);
		
		self_.alert('success',obj.msg);
	    }
	    
	},'json');
    }
    
    $('#'+buttonid).click(function()
    {
	var post = {'page' : self_.page};
	
	if( self_.orderid !== undefined )
	    post.order_id = self_.orderid;
	
	$.post('/m0r1/ajax/getbonuspage',post,function(obj){
	    $('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-body-'+self_.modalid).html(obj.bonuses);
	    $('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:first + div').html(obj.info);
	    
	    self_.countPages = obj.countPages;
	    
	    if( self_.countPages > 1 )
	    {
		$('div#'+self_.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+self_.modalid+' div:last a').removeClass('disabled');
	    }
	    
	    $('div#'+self_.modalid).modal('show');
	    
	},'json');
	
    });
    
    $('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+this.modalid+' div:first a').click(function()
    {
	self_.chPage(self_.page - 1);
    });

    $('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+this.modalid+' div:last a').click(function()
    {
	self_.chPage(self_.page + 1);
    });
    
    $(document).on('click','div.m0r1bbw-count-panel > div.row > div.col-md-4:first-child > div',function(){
	//minus
	let count = parseInt($(this).parent().parent().parent().attr('data-count'));
	
	if ( isNaN( count ) || count <= 1)
	    return;
	
	if ( $(this).parent().parent().parent().next().hasClass( 'm0r1bbw-shop-panel-used' ) )
	{
	    if ( !$(this).parent().parent().parent().is('[data-count-backup]') )
	    {
		$(this).parent().parent().parent().attr('data-count-backup',count);
		$(this).parent().parent().parent().next().children("div:eq(0)").show();
		$(this).parent().parent().parent().next().children("div:eq(1)").hide();
	    }
	}
	
	count--;
	$(this).parent().parent().parent().attr('data-count',count);
	
	$(this).parent().next().find('div').html(count);
    });
    $(document).on('click','div.m0r1bbw-count-panel > div.row > div.col-md-4:first-child + div + div > div',function(){
	//plus
	let count = parseInt($(this).parent().parent().parent().attr('data-count'));
	let total = parseInt($(this).parent().parent().parent().attr('data-total-count'));
	
	if ( isNaN( count ) || isNaN( total ) || total == count )
	    return;

	if ( $(this).parent().parent().parent().next().hasClass( 'm0r1bbw-shop-panel-used' ) )
	{
	    if ( !$(this).parent().parent().parent().is('[data-count-backup]') )
	    {
		$(this).parent().parent().parent().attr('data-count-backup',count);
		$(this).parent().parent().parent().next().children("div:eq(0)").show();
		$(this).parent().parent().parent().next().children("div:eq(1)").hide();
	    }
	}


	count++;
	$(this).parent().parent().parent().attr('data-count',count);
	
	$(this).parent().prev().find('div').html(count);
    });
    
    $(document).on('click','div.m0r1bbw-shop-panel > div:first-child > a:first-child',function(){
	var post = {};
	
	post.id = parseInt( $(this).parent().parent().parent().attr('data-id') );
	
	if ( isNaN( post.id ) )
	    return;
	
	post.count = parseInt( $(this).parent().parent().prev().attr('data-count') );

	if ( isNaN ( post.count ) )
	    return;
	
	self_.Shop(post);
    });
    
    $(document).on('click','div.m0r1bbw-shop-panel > div:first-child > a:first-child + a',function(){
	var panel = $(this).parent().parent();
	
	panel.prev().children("div.row").children('div.col-md-4:eq(1)').children('div:eq(0)').html(panel.prev().attr('data-count-backup'));
	panel.prev().attr('data-count',panel.prev().attr('data-count-backup'));
	panel.prev().removeAttr('data-count-backup');
	
	panel.children('div:eq(0)').hide();
	panel.children('div:eq(1)').show();
    });
    
    $(document).on('click','div.m0r1bbw-shop-panel > div:first-child + div > a:first-child',function()
    {
	var post = {};
	let panel = $(this).parent().parent().parent();
	
	post.id = parseInt( panel.attr('data-id') );
	
	if( isNaN( post.id ) )
	    return;
	
	self_.UnShop(post);
    });
    
    $('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+this.modalid+' div:first a').addClass('disabled');
    $('div#'+this.modalid+' div.modal-dialog div.modal-content div.modal-body div#m0r1-bbw-footer-'+this.modalid+' div:last a').addClass('disabled');
}

M0r1BBW.autoIdPrefix = 'm0r1bbw';
M0r1BBW.counter = 0;