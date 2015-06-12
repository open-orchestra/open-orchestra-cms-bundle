widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.multiVersion
    $.ajax
      type: "GET"
      url: view.options.multiVersion.self_version
      async: false
      success: (response) ->
        collection = new VersionviewElement
        collection.set response
        collectionName = collection.get('collection_name')
        viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showVersion')
        versionSelectView = new viewClass(
          currentVersion: view.options.multiVersion
          versions: collection.get(collectionName)
          domContainer: view.$el.find('#version-selectbox')
        )
        return
    if view.options.element
      $('.js-widget-title', view.options.domContainer).html view.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
        element: view.options.element
      )
