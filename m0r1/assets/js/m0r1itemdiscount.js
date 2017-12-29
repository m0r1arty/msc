var M0r1ItemDiscount = function( mi )
{
    var self_ = this;
    this.mi = mi;
    this.btn = null;
    
    this.addItem = function()
    {
	$("div.m0r1itemdiscount > div > select").select2();
    }
    
    this.saveConf = function( evt )
    {
	var btn = $(evt.currentTarget);
	var div = btn.parent().parent();
	var obj = {};
	
	btn.addClass("disabled");
	self_.btn = btn;
	
	obj.id = parseInt( div.parent().parent().attr('data-id') );
	obj.pos = parseInt( div.parent().parent().attr('data-pos') );
	obj.disc_type_id = parseInt( $( "div:first-child > select",div ).val() );
	obj.disc_id = parseInt( $( "div:first-child + div > select",div ).val() );
	
	self_.mi.confItem(obj);
    }
    
    this.confItem = function()
    {
	self_.btn.removeClass("disabled");
    }
    
    this.mi.on('Core|Discount','append',this.addItem);
    this.mi.on('Core|Discount','insert',this.addItem);
    this.mi.on('Core|Discount','conf',this.confItem);
    
    $("div.m0r1itemdiscount > div > select").select2();
    
    $(document).on('click','div.m0r1itemdiscount > div + div + div > a',this.saveConf);
}

var g_M0r1ItemDiscount;

$(document).ready( function()
{

 g_M0r1ItemDiscount = new M0r1ItemDiscount( g_M0r1Item );
});

