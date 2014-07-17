$('body').on('change', '.refresh', function(){
	$(this).parents('form').refreshForm();
});

(function($){
	$.fn.refreshForm = function(params){
		var target = $(this);
		var selector = $.getSelector($(this))
		if(!params){
			params = target.serializeArray();
		}
		params = params.concat({'name': 'refresh', 'value': true});
	    $.ajax({
	        'type': 'POST',
	        'url': target.attr('action'),
	        'success': function(response){
	    		target.html($('<div />').append(response.data).find(selector).html());
	        },
	        'data': params,
	        'dataType': 'json',
	        'async': false
	    });
	}
})(jQuery);
