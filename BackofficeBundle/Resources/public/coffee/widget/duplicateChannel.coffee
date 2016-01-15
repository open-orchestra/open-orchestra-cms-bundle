widgetChannel.bind 'ready', (view) ->
  if view.options and view.options.duplicate
    viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showDuplicate')
    viewClass.prototype.addConcurrency()
    new viewClass(
      domContainer: view.$el.find('#entity-duplicate')
      currentDuplicate: view.options.duplicate
      entityType: view.options.entityType)
  return
