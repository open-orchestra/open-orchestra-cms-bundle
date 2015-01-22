addParameter = (element, label, value) ->
  value = element.get(label) if (typeof value == 'undefined' and element.get(label))
  if(typeof value != 'undefined')
    links = element.get('links')
    links['_self_form'] = links['_self_form'] + label + "=" + value + "&"
    for i of links
      links[i] = links[i].replace "/([?&]" + label + "=)[^&#]+/", "$1" + value
    element.set('links', links);
  return element

tableViewLoad = (link, entityType, entityId, language, version) ->
  target = "#content"
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  title = link.text()
  listUrl = appRouter.generateUrl('listEntities',
    entityType: entityType
  )
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        founded = false
        elements = new TableviewElement()
        elements.set response
        if entityId
          collection_name = elements.get("collection_name")
          collection = elements.get(collection_name)
          $.each collection, (rank, values) ->
            element = new TableviewModel
            element.set values
            if entityId is element.get('id')
              if language != undefined
                link = element.get('links')._self_without_parameters + '?language=' + language
                link = link + '&version=' + version if version != undefined
                tableViewLoadSpecificElement(link, title, listUrl)
                founded = true
                return false
              $.ajax
                url: element.get('links')._self_form
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    listUrl: listUrl
                    element: element
                  view = new FullPageFormView(options)
                  appRouter.setCurrentMainView view
              founded = true
              return false
        unless founded
          view = new TableviewCollectionView(
            elements: elements
            displayedElements: displayedElements
            title: title
            listUrl: listUrl
            el: target
          )
          appRouter.setCurrentMainView view

tableViewLoadSpecificElement = (link, title, listUrl) ->
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
            listUrl: listUrl
            element: element
          view = new FullPageFormView(options)
          appRouter.setCurrentMainView view
