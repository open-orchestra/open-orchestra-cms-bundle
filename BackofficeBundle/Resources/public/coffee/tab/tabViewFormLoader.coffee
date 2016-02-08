tabViewFormLoad = (options) ->
  @options = options
  @panels = getPanelsLink(@options.element.get('links'))
  tabViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showTab')
  @tabView = new tabViewClass(@options)
  loaderContext = @
  for panel, i in panels
    if panel.id == 'form' and @options.response
      this.generatePanelView(@options.response, i)
    else
      do (panel, i) ->
        $.ajax
          url: panel.link
          method: "GET"
          success: (response) ->
            loaderContext.generatePanelView(response, i)

generatePanelView = (response, position) ->
  elementTabViewClass = appConfigurationView.getConfiguration(@options.entityType + '_tab_' + @panels[position].id, 'editEntityTab')

  elementTabViewClass::onViewReady = ->
    if !@options.submitted
      @options.callback this
    return

  callback = ((tabView, panel, position) ->
    (view) ->
      tabView.addPanel $('[data-title]', view.$el).data('title'), panel.id, view, panel.isActive, position
      return
  )(@tabView, @panels[position], position)
  new elementTabViewClass(
    response: response
    entityType: @options.entityType
    listUrl: @options.listUrl
    callback: callback)

getPanelsLink = (links) ->
  panels = []
  if links._self_form
    panels.push {link:links._self_form, isActive:true, id:'form'}
  for key in Object.keys(links)
    if infos = key.match(/^_self_panel_(.*)/)
      panels.push {link:links[key], isActive:false, id:infos[1]}
  return panels
