$(document).ready(function()
{
    $('form#form-data').submit(function(evt)
    {
	var ret = [];
	var item = {};
	var pasitem;
	
	$("ul > li > input[data-source-id]").each(function()
	{
	    item = {};
	    
	    item.name = $(this).attr("data-source-id");
	    
	    pasitem = $("#"+item.name).parent().parent().parent().parent().next();
	    
	    if( pasitem[0].tagName === 'DIV' )
	    {
		item.processValueAs = $("div > select",pasitem[0]).val();
	    }
	    
	    ret.push( item );
	});
	
	$.cookie("m0r1importexport",JSON.stringify(ret),{ expires : 365 });
	
	evt.stopPropagation();
	return false;
    });
});

function m0r1RestoreStateFromCookies()
{
    var ret = $.cookie("m0r1importexport");
    var pasitem;

    if( ret !== undefined )
    {
        ret = JSON.parse( ret );

        ret.forEach( function( item )
        {
    	    console.log(item);
            $("#"+item.name).prop("checked","checked").trigger("change");
            
            if( item.processValueAs !== undefined )
            {
        	pasitem = $("#"+item.name).parent().parent().parent().parent().next();
        	
        	if( pasitem[0].tagName === 'DIV' )
        	{
        	    $("div > select",pasitem[0]).val( item.processValueAs );
        	}
            }
        });
    }
}
