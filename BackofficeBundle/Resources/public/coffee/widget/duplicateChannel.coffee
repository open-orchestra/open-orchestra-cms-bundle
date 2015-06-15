widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.duplicate
    viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showDuplicate')
    new viewClass(
        domContainer: view.$el.find('#entity-duplicate')
        currentDuplicate: view.options.duplicate
        entityType: view.options.entityType
      )
