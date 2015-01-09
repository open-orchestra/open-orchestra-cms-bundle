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
              links = element.get('links')
              links['_self_form'] = links['_self_form'] + "?"
              element.set('links', links);
              element = addParameter(element, 'language', language)
              element = addParameter(element, 'version', version)
              $.ajax
                url: element.get('links')._self_form
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    listUrl: listUrl
                    element: element
                  options = $.extend(options, multiLanguage:
                    language_list : element.get('links')._language_list
                    language : language
                    path: 'showEntityWithLanguage'
                  ) if element.get('links')._language_list
                  options = $.extend(options, multiStatus:
                    language: language
                    version: version
                    status_list: element.get('links')._status_list
                    status: element.get('status')
                    self_status_change: element.get('links')._self_status_change
                  ) if element.get('links')._status_list
                  options = $.extend(options, multiVersion:
                    language: language
                    version: version
                    self_version: element.get('links')._self_version
                    path: 'showEntityWithLanguageAndVersion'
                  ) if element.get('links')._self_version
                  options = $.extend(options, duplicate:
                    language: language
                    self_duplicate: element.get('links')._self_duplicate
                    path: 'showEntityWithLanguage'
                  ) if element.get('links')._self_duplicate
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
