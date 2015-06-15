tableViewLoad = (link, entityType, page, entityId, language, version, sourceLanguage) ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  translatedHeader = link.data('translated-header').replace(/\s/g, '').split(",") if link.data('translated-header') != undefined
  visibleElements = link.data('visible-elements').replace(/\s/g, '').split(",") if link.data('visible-elements') != undefined
  order = link.data('order').replace(/\s/g, '').split(",") if link.data('order') != undefined
  title = link.text()
  new TableviewCollectionView(
    displayedElements: displayedElements
    translatedHeader: translatedHeader || displayedElements
    visibleElements: visibleElements || []
    order: order
    title: title
    page: page
    url : link.data('url')
    entityType: entityType
    domContainer: $("#content")
  )

entityViewLoad = (link, entityType, page, entityId, language, version, sourceLanguage) ->
  title = link.text()
  $.ajax
    url: link.data('url')
    method: 'GET'
    data:
      entityId: entityId
      language: language
      source_language: sourceLanguage
      version: version
    success: (response) ->
      collection_name = response.collection_name
      values = response[collection_name][0]
      element = new TableviewModel
      element.set values
      if language != undefined
        link = element.get('links')._self_without_parameters + '?language=' + language
        link = link + '&source_language=' + sourceLanguage if sourceLanguage != undefined
        link = link + '&version=' + version if version != undefined
        $.ajax
          url: link
          method: 'GET'
          async: false
          success: (response) ->
            element = new TableviewElement()
            element.set response
            redirectUrl = appRouter.generateUrl('showEntityWithLanguageAndVersion', appRouter.addParametersToRoute(
              'entityId': element.get('id')
              'language': element.get('language')
              'version' : element.get('version')
            ))
            Backbone.history.navigate(redirectUrl)
      links = element.get('links')
      panelKeys = []
      for key in Object.keys(links)
        if /^_self_panel_/.test(key)
          appConfigurationView.setConfiguration(entityType, 'edit', FullPagePanelView)
          break
      $.ajax
        url: element.get('links')._self_form
        method: "GET"
        success: (response) ->
          options =
            html: response
            title: title
            entityType: entityType
            element: element
            domContainer: $('#content')
          viewClass = appConfigurationView.getConfiguration(entityType, 'edit')
          new viewClass(options)
