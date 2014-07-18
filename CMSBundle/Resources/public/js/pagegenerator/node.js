var allowed_object = ['areas', 'blocks'];

function returnActions(options, length, direction){
	return {
		'fa fa-cog' : [
       	   	'$("#dialog-' + options.type + '").data(' + JSON.stringify(options) + ');',
       	   	'$("#dialog-' + options.type + '").data("source", $(this).closest("li"));',
       	   	'$("#dialog-' + options.type + '").fromJsToForm();',
       		'$("#dialog-' + options.type + '").dialog( "open" );'
       	]
	};
}

deleteDialogIfExists('dialog-node');
$('#dialog-node').dialog($.extend(getDialogParameter(), {"addArray" : []}));
$('#content div[role="content"]').html('');
$('#content div[role="content"]').model({"type" : "node", "resizable" : false});

$('#node_templateId').change(function(e){
	var value = $(this).val();
	var data = $(this).parents('#dialog-node').data();
    $.ajax({
        'type': 'GET',
        'url': 'http://127.0.0.1:8080/orchestra/phporchestra/src/symfony2/web/app_dev.php/admin/template/ajaxRequest/' + value,
        'success': function(response){
    		data.container.data('settings').areas = response.data;
    		data.source.empty();
    		data.source.model({'path' : data.path, 'parent_path': data.parent_path, 'type' : data.type});
        },
        'dataType': 'json',
        'async': false
    });
});