addParameter = (element, label, value) ->
  value = element.get(label) if (typeof value == 'undefined' and element.get(label))
  if(typeof value != 'undefined')
    links = element.get('links')
    links['_self_form'] = links['_self_form'] + label + "=" + value + "&"
    for i of links
      links[i] = links[i].replace "/([?&]" + label + "=)[^&#]+/", "$1" + value
    element.set('links', links);
  return element

tableViewLoad = (link, entityType, entityId, language, version, add, sourceLanguage) ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  translatedHeader = link.data('translated-header').split(",") if link.data('translatedHeader') != undefined
  order = link.data('order').replace(/\s/g, '').split(",") if link.data('order') != undefined
  title = link.text()
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      founded = false
      elements = new TableviewElement()
      elements.set response
      if add != undefined
        $.ajax
          url: elements.get('links')._self_add
          method: "GET"
          success: (response) ->
            viewClass = appConfigurationView.getConfiguration(entityType, 'add')
            new window[viewClass](
              html: response
              title: title
              entityType: entityType
              element: elements
              extendView: [ 'generateId' ]
            )
        founded = true
      if entityId != undefined
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
              $.ajax
                url: element.get('links')._self_form
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    entityType: entityType
                    element: element
                  viewClass = appConfigurationView.getConfiguration(entityType, 'edit')
                  new window[viewClass](options)
              founded = true
      unless founded
        new TableviewCollectionView(
          elements: elements
          displayedElements: displayedElements
          translatedHeader: translatedHeader
          order: order
          title: title
          entityType: entityType
          domContainer: $("#content")
        )

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
          viewClass = appConfigurationView.getConfiguration(entityType, 'edit')
          new window[viewClass](options)
    error: ->
      displayed = false
  return displayed
