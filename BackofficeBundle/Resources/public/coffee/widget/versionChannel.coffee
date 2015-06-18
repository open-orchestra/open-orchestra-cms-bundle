widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.multiVersion
    $.ajax
      type: "GET"
      url: view.options.multiVersion.self_version
      async: false
      success: (response) ->
        versions = new VersionModel
        versions.set response
        collectionName = versions.get('collection_name')
        viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showVersionSelect')
        new viewClass(
          currentVersion: view.options.multiVersion
          versions: versions.get(collectionName)
          domContainer: view.$el.find('#version-selectbox')
          entityType: view.options.entityType
        )
        return
    if view.options.element
      $('.js-widget-title', view.options.domContainer).html view.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
        element: view.options.element
      )
