tabViewFormLoad = (options) ->
  tabViewClass = appConfigurationView.getConfiguration(options.entityType, 'showTab')

  tabView = new tabViewClass(options)
  panels = getPanelsLink(options.element.get('links'))

  for panel, i in panels
    do (panel, i) ->
      $.ajax
        url: panel.link
        method: "GET"
        success: (response) ->
          elementTabViewClass = appConfigurationView.getConfiguration(options.entityType+'_tab_'+panel.id, 'editEntityTab')
          view = new elementTabViewClass(
            html: response,
            entityType: options.entityType,
            listUrl: options.listUrl
            tabView: tabView
            panel: panel
            rank: i
          )

getPanelsLink = (links) ->
  panels = []
  if links._self_form
    panels.push {link:links._self_form, isActive:true, id:'form'}
  for key in Object.keys(links)
    if infos = key.match(/^_self_panel_(.*)/)
      panels.push {link:links[key], isActive:false, id:infos[1]}
  return panels
