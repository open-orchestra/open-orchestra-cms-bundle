tabViewFormLoad = (options) ->
  tabView = new TabView(options)
  panels = getPanelsLink(options.element.get('links'))
  for panel in panels
    do (panel) ->
      $.ajax
        url: panel["link"]
        method: "GET"
        success: (response) ->
          view = new TabElementFormView(
            html: response,
            entityType: options.entityType,
            listUrl: options.listUrl
          )
          tabView.addPanel($(response).data('title'), panel['id'], view)

getPanelsLink = (links) ->
  panels = []
  if links._self_form
      panels.push {link:links._self_form, isActive:true, id:'form'}
  for key in Object.keys(links)
    if infos = key.match(/^_self_panel_(.*)/)
      panels.push {link:links[key], isActive:false, id:infos[1]}
  return panels
