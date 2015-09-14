TabView = OrchestraView.extend(

  initialize: (options) ->
    @options = options
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
    id = 'tab-'+id

    a = $('<a>').attr('href', '#' + id).attr('data-toggle', 'tab').text(title);
    if position?
      @insertTabNavPosition($('<li>').attr('tab-position', position).append(a), position)
    else
      @$tabNav.append($('<li>').attr('tab-position', position).append(a));

    content = $('<div>').attr('id',id).addClass('tab-pane').html(view.$el)
    @$tabContent.append(content)

    a.tab('show') if active

  insertTabNavPosition : (tab, position) ->
    for li in  @$tabNav.children('li')
      if parseInt($(li).attr('tab-position')) > position
        tab.insertBefore($(li))
        return
    @$tabNav.append(tab)

  onViewReady: ->
    viewContext = @
    for panel, i in @options.panels
      do (panel, i) ->
        $.ajax
          url: panel.link
          method: "GET"
          success: (response) ->
            elementTabViewClass = appConfigurationView.getConfiguration(viewContext.options.entityType+'_tab_'+panel.id, 'editEntityTab')
            view = new elementTabViewClass(
              html: response,
              entityType: viewContext.options.entityType,
              listUrl: viewContext.options.listUrl
            )
            viewContext.addPanel($(response).data('title'), panel.id, view, panel.isActive, i)

)
