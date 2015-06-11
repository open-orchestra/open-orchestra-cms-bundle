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
        versionSelectView = new VersionSelectView(
          currentVersion: view.options.multiVersion
          domContainer: view.$el.find('#version-selectbox')
        )
        for version of collection.get(collectionName)
          versionElement = new VersionviewModel
          versionElement.set collection.get(collectionName)[version]
          new VersionView(
            element: versionElement
            currentVersion: view.options.multiVersion
            domContainer: versionSelectView.$el
          )
        return
    if view.options.element
      $('.js-widget-title', view.options.domContainer).html view.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
        element: view.options.element
      )
