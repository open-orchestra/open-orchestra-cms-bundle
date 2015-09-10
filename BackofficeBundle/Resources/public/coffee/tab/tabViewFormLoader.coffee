tabViewFormLoad = (options) ->
  tabViewClass = appConfigurationView.getConfiguration(options.entityType, 'showTab')

  options. panels = getPanelsLink(options.element.get('links'))
  tabView = new tabViewClass(options)

getPanelsLink = (links) ->
  panels = []
  if links._self_form
      panels.push {link:links._self_form, isActive:true, id:'form'}
  for key in Object.keys(links)
    if infos = key.match(/^_self_panel_(.*)/)
      panels.push {link:links[key], isActive:false, id:infos[1]}
  return panels
