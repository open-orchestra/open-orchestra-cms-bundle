widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.newVersion
    viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showNewVersion')
    new viewClass(
        domContainer: view.$el.find('#entity-new-version')
        currentNewVersion: view.options.newVersion
        entityType: view.options.entityType
      )
