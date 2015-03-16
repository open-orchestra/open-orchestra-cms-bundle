addParameter = (element, label, value) ->
  value = element.get(label) if (typeof value == 'undefined' and element.get(label))
  if(typeof value != 'undefined')
    links = element.get('links')
    links['_self_form'] = links['_self_form'] + label + "=" + value + "&"
    for i of links
      links[i] = links[i].replace "/([?&]" + label + "=)[^&#]+/", "$1" + value
    element.set('links', links);
  return element

tableViewLoad = (link, entityType, entityId, language, version, add) ->
  target = "#content"
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  order = link.data('order').replace(/\s/g, '').split(",") if link.data('order') != undefined
  title = link.text()
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
        if add
          $.ajax
            url: elements.get('links')._self_add
            method: "GET"
            success: (response) ->
              view = new FullPageFormView(
                html: response
                title: title
                entityType: entityType
                element: elements
                triggers: [
                  {
                    event: "focusout input.generate-id-source"
                    name: "generateId"
                    fct: generateId
                  }
                  {
                    event: "blur input.generate-id-dest"
                    name: "stopGenerateId"
                    fct: stopGenerateId
                  }
                ]
              )
          founded = true
          return false
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
                tableViewLoadSpecificElement(link, title, entityType)
                founded = true
                return false
              $.ajax
                url: element.get('links')._self_form
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    entityType: entityType
                    element: element
                  view = new FullPageFormView(options)
                  appRouter.setCurrentMainView view
              founded = true
              return false
        unless founded
          view = new TableviewCollectionView(
            elements: elements
            displayedElements: displayedElements
            order: order
            title: title
            entityType: entityType
            el: target
          )
          appRouter.setCurrentMainView view

tableViewLoadSpecificElement = (link, title, entityType) ->
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
          view = new FullPageFormView(options)
          appRouter.setCurrentMainView view
