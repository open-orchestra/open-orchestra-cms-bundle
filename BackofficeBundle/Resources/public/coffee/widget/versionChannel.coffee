versionChannel = Backbone.Wreqr.radio.channel('version')

versionChannel.commands.setHandler 'ready', (view) ->
  $.ajax
    type: "GET"
    url: view.options.multiVersion.self_version
    success: (response) ->
      collection = new VersionviewElement
      collection.set response
      collectionName = collection.get('collection_name')
      for version of collection.get(collectionName)
        versionElement = new VersionviewModel
        versionElement.set collection.get(collectionName)[version]
        new VersionView(
          element: versionElement
          currentVersion: view.options.multiVersion
          domContainer: view.$el.find('#version-selectbox')
        )
      return
