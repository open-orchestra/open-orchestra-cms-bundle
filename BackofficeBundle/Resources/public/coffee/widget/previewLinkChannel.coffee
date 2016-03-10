widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.node && view.options.node.get('preview_links').length > 0
    viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showPreviewLinks')
    new viewClass(
      domContainer: view.$el
      previewLinks: view.options.node.get('preview_links')
      entityType: view.options.entityType
      widget_index: 3
    )
