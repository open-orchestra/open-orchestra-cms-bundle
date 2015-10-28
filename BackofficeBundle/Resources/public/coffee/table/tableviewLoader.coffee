tableViewLoad = (link, entityType, page) ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  translatedHeader = link.data('translated-header').split(",") if link.data('translated-header') != undefined
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
        viewClass = appConfigurationView.getConfiguration(entityType, 'addEntity')
        new viewClass(options)
  else
    data = {
      entityId: entityId
      source_language: sourceLanguage
    }
    data.language = language if language?
    data.version = version if version?
    $.ajax
      url: link.data('url')
      method: 'GET'
      data: data
      success: (response) ->
        collection_name = response.collection_name
        values = response[collection_name][0]
        element = new TableviewModel
        element.set values
        if language? and sourceLanguage?
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
        panels = false
        for key in Object.keys(links)
          if /^_self_panel_/.test(key)
            panels = true
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
            if panels
              tabViewFormLoad(options)
            else
              viewClass = appConfigurationView.getConfiguration(entityType, 'editEntity')
              new viewClass(options)
