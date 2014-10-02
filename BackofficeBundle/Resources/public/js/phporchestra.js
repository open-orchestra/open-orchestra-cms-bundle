// fire links with targets on menu to open
$(document).on('click', 'nav a[target="_menu"]', function(e) {
    e.preventDefault();
    $this = $(e.currentTarget);
    window.setTimeout(function() {
        if (!$this.hasClass('menu-opened')) {
            $this.addClass("menu-opened");
            
            $.ajax({
                type : "GET",
                url : $this.attr('href'),
                dataType : 'json',
                cache : false,
                success : function(data) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<li><a href="' + data[i].url + '">' + data[i].label + '</a></li>'
                    }
                    $this.next().html(html);
                    return false;
                },
                error : function(xhr, ajaxOptions, thrownError) {
                    $this.next().html('<li><a href=""><i class="fa fa-times-circle"></i> Error</a></li>');
                },
                async : false
            });
            
        } else {
            $this.removeClass("menu-opened");
            $this.next().html('<li><a href="">Loading ...</a></li>');
        }
        
    }, 200);
});

function displayLoader(element)
{
    if (typeof element == 'undefined') {
        element = '#content';
    }
    $(element).html('<h1><i class="fa fa-cog fa-spin"></i> Loading...</h1>');
    
    return true;
}

function switchLoaderFullPage(state)
{
    if (typeof state == 'undefined') {
        state = 'on';
    }
    
    if (state == 'on') {
        $(document).scrollTop(0);
        $('#loader-fullpage').show();
    } else {
        $('#loader-fullpage').hide();
    }
    
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

function displayMenu()
{
    var selectedPath = "#" + Backbone.history.fragment;
    
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
        }
    });
}
