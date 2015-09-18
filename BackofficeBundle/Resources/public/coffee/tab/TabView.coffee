TabView = OrchestraView.extend(

  initialize: (options) ->
    @options = options
    @options.panels = []
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tabView'
    ]
    return

  initTab: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tabView')
    @$tabNav = $('ul.nav', @$el)
    @$tabContent = $('.tab-content', @$el)

  render: ->
    @initTab()
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title


  addPanel: (title, id, view, active, position) ->
    if @$tabNav? and @$tabContent?
      @createPanel(title, id, view, active, position)
    else
      @options.panels.push {title: title, id: id, view: view, active: active, position: position}

  insertTabNavPosition : (tab, position) ->
    for li in  @$tabNav.children('li')
      if parseInt($(li).attr('tab-position')) > position
        tab.insertBefore($(li))
        return
    @$tabNav.append(tab)

  createPanel : (title, id, view, active, position) ->
    id = 'tab-'+id

    a = $('<a>').attr('href', '#' + id).attr('data-toggle', 'tab').text(title);
    if position?
      @insertTabNavPosition($('<li>').attr('tab-position', position).append(a), position)
    else
      @$tabNav.append($('<li>').attr('tab-position', position).append(a));

    content = $('<div>').attr('id',id).addClass('tab-pane').html(view.$el)
    @$tabContent.append(content)

    a.tab('show') if active

  onViewReady: ->
    if @options.panels.length > 0
      for panel, i in @options.panels
        @createPanel(panel.title, panel.id, panel.view, panel.active, panel.position)
      @options.panels = []

)
