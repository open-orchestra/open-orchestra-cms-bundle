tableViewLoad = (link, entityType, page, flashMessage = "") ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  translatedHeader = link.data('translated-header').replace(/\s/g, '').split(",") if link.data('translated-header') != undefined
  visibleElements = link.data('visible-elements').replace(/\s/g, '').split(",") if link.data('visible-elements') != undefined
  order = link.data('order').replace(/\s/g, '').split(",") if link.data('order') != undefined
  title = link.text()
  viewClass = appConfigurationView.getConfiguration(entityType, 'showTableCollection')
  new viewClass(
    displayedElements: displayedElements
    translatedHeader: translatedHeader || displayedElements
    visibleElements: visibleElements || []
    order: order
    title: title
    page: page
    url : link.data('url')
    entityType: entityType
    domContainer: $("#content")
    flashMessage: flashMessage
  )

entityViewLoad = (link, entityType, entityId, language, version, sourceLanguage) ->
  title = link.text()
  if !entityId
    $.ajax
      url: link.data('add')
      method: "GET"
      success: (response) ->
        options =
          html: response
          title: title
          entityType: entityType
          domContainer: $('#content')
          type: "creation"
        viewClass = appConfigurationView.getConfiguration(entityType, 'addEntity')
        new viewClass(options)
  else
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
        if language?
          link = element.get('links')._self_without_parameters + '?language=' + language
          link = link + '&source_language=' + sourceLanguage if sourceLanguage != undefined
          link = link + '&version=' + version if version != undefined
          $.ajax
            url: link
            method: 'GET'
            async: false
            success: (response) ->
              element = new TableviewModel()
              element.set response
              redirectUrl = appRouter.generateUrl('showEntity', appRouter.addParametersToRoute(
                'entityId': element.get('id')
                'language': element.get('language')
                'version' : element.get('version')
              ))
              Backbone.history.navigate(redirectUrl)
        links = element.get('links')
        for key in Object.keys(links)
          if /^_self_panel_/.test(key)
            appConfigurationView.setConfiguration(entityType, 'editEntity', FullPagePanelView)
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
            viewClass = appConfigurationView.getConfiguration(entityType, 'editEntity')
            new viewClass(options)
