widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.node && typeof(view.options.node.get('preview_links')) != "undefined"
    viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showPreviewLinks')
    new viewClass(
      previewLinks: view.options.node.get('preview_links')
      entityType: view.options.entityType
    )
