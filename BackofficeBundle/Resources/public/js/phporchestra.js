function displayLoader(element)
{
    if (typeof element == 'undefined') {
        element = '#content';
    }
    $(element).html('<h1><i class="fa fa-cog fa-spin"></i> Loading...</h1>');
    
    return true;
}

function orchestraAjaxLoad(url, method, successCallback)
{
    displayLoader();
    
    if (typeof method == 'undefined') {
        method = 'POST';
    }
    
    $.ajax({
        url: url,
        type: method,
        success: function(response) {
            if (response.success) {
                window.location.hash = response.data;
            } else {
                $('#content').html(response);
                if (typeof successCallback !== 'undefined') {
                    successCallback();
                }
            }
        }
    });
}

function callAndReload(action)
{
    displayLoader();
    $.post(action, function(response) {
        if (response.success) {
            Backbone.history.navigate('#', true);
            window.location.reload();
        }
    });
}

function displayMenu(route)
{
    var selectedPath;

    if (typeof route !== 'undefined') {
        selectedPath = "#" + route;
    } else {
        selectedPath = "#" + Backbone.history.fragment;
    }
    
    $.ajax({
        url: $('#left-panel nav').data("url"),
        type: 'GET',
        success: function(response) {
            // render html
            $('#left-panel nav').replaceWith(response);
            
            // create the jarvis menu
            var opts = {
                accordion : true,
                speed : $.menu_speed,
                closedSign : '<em class="fa fa-expand-o"></em>',
                openedSign : '<em class="fa fa-collapse-o"></em>'
            };
            $('nav ul').jarvismenu(opts);
            
            // tag selected path 
            $('nav li:has(a[href="' + selectedPath + '"])').addClass("active");
            
            // open selected path
            $('#left-panel nav').find("li.active").each(function() {
                $(this).parents("ul").slideDown(opts.speed);
                $(this).parents("ul").parent("li").find("b:first").html(opts.openedSign);
                $(this).parents("ul").parent("li").addClass("open")
            });

            if (typeof route !== 'undefined') {
                Backbone.history.navigate(route, {trigger: true});
            }
        }
    });
}
