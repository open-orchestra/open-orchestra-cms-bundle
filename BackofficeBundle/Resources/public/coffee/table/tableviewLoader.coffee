addParameter = (element, label, value) ->
  value = element.get(label) if (typeof value == 'undefined' and element.get(label))
  if(typeof value != 'undefined')
    links = element.get('links')
    links['_self_form'] = links['_self_form'] + label + "=" + value + "&"
    for i of links
      links[i] = links[i].replace "/([?&]" + label + "=)[^&#]+/", "$1" + value
    element.set('links', links);
  return element

tableViewLoad = (link, entityType, page, entityId, language, version, sourceLanguage) ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  translatedHeader = link.data('translated-header').replace(/\s/g, '').split(",") if link.data('translated-header') != undefined
  visibleElements = link.data('visible-elements').replace(/\s/g, '').split(",") if link.data('visible-elements') != undefined
  order = link.data('order').replace(/\s/g, '').split(",") if link.data('order') != undefined
  title = link.text()
  if !entityId?
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
  else
    $.ajax
      url: link.data('url')
      method: 'GET'
      success: (response) ->
        elements = new TableviewElement()
        elements.set response
        collection_name = elements.get("collection_name")
        collection = elements.get(collection_name)
        $.each collection, (rank, values) ->
          element = new TableviewModel
          element.set values
          if entityId is element.get('id')
            if language != undefined
              link = element.get('links')._self_without_parameters + '?language=' + language
              link = link + '&source_language=' + sourceLanguage if sourceLanguage != undefined
              link = link + '&version=' + version if version != undefined
              founded = tableViewLoadSpecificElement(link, title, entityType)
            else
              links = element.get('links')
              panelKeys = []
              for key in Object.keys(links)
                if /^_self_panel_/.test(key)
                  panelKeys.push(key)
              if panelKeys.length > 0
                appConfigurationView.setConfiguration(entityType, 'edit', FullPagePanelView)
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

tableViewLoadSpecificElement = (link, title, entityType) ->
  displayed = true
  $.ajax
    url: link
    method: 'GET'
    success: (response) ->
      element = new TableviewElement()
      element.set response
      redirectUrl = appRouter.generateUrl('showEntityWithLanguageAndVersion', appRouter.addParametersToRoute(
        'entityId': element.get('id')
        'language': element.get('language')
        'version' : element.get('version')
      ))
      Backbone.history.navigate(redirectUrl)
      $.ajax
        url: element.get('links')._self_form
        method: 'GET'
        success: (response) ->
          options =
            html: response
            title: title
            entityType: entityType
            element: element
            domContainer: $('#content')
          viewClass = appConfigurationView.getConfiguration(entityType, 'edit')
          new viewClass(options)
    error: ->
      displayed = false
  return displayed
