TabView = OrchestraView.extend(

  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tabView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tabView')

    @$tabNav = $('ul.nav', @$el)
    @$tabContent = $('.tab-content', @$el)

    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title


  addPanel: (title, id, view) ->
    id = 'tab-'+id

    a = $('<a>').attr('href', '#' + id).attr('data-toggle', 'tab').text(title);
    @$tabNav.append($('<li>').append(a));

    content = $('<div>').attr('id',id).addClass('tab-pane').html(view.$el)
    @$tabContent.append(content)

    a.tab('show') if (@$tabNav.children('li').length <= 1)
)