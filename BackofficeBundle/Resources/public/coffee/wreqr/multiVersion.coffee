widgetChannel.commands.setHandler 'initMultiVersion', (view) ->
  view.events['change .version-selectbox-' + view.cid] = 'changeVersion'
  view.changeVersion = (event) ->
    redirectUrl = appRouter.generateUrl(view.options.multiVersion.path, appRouter.addParametersToRoute(
      version: event.currentTarget.value
      language: view.options.multiVersion.language
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})

widgetChannel.reqres.setHandler 'initMultiVersion', ->
  return ['elementChoice', 'elementTitle']

widgetChannel.commands.setHandler 'addMultiVersion', (view) ->
    $.ajax
      type: "GET"
      url: view.options.multiVersion.self_version
      success: (response) ->
        collection = new TableviewElement
        collection.set response
        collectionName = collection.get('collection_name')
        for version of collection.get(collectionName)
            versionElement = new TableviewModel
            versionElement.set collection.get(collectionName)[version]
            new VersionView(
              element: versionElement
              version: view.options.multiVersion.version
              el: view.$el.find('#version-selectbox')
            )
        return
