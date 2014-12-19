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
            elementModel = new TableviewModel
            elementModel.set values
            if entityId is elementModel.get('id')
              language = elementModel.get('language') if (typeof language == 'undefined')
              version = elementModel.get('version') if (typeof version == 'undefined')
              url = elementModel.get('links')._self_form + '?language=' + language + '&version=' + version
              $.ajax
                url: url
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    listUrl: listUrl
                    element: elementModel
                  options = $.extend(options, multiLanguage:
                    language_list : elementModel.get('links')._language_list
                    language : language
                    path: 'showEntityWithLanguage'
                  ) if elementModel.get('links')._language_list
                  options = $.extend(options, multiStatus:
                    language: language
                    version: version
                    status_list: elementModel.get('links')._status_list
                    status: elementModel.get('status')
                    self_status_change: elementModel.get('links')._self_status_change
                  ) if elementModel.get('links')._status_list
                  options = $.extend(options, multiVersion:
                    language: language
                    version: version
                    self_version: elementModel.get('links')._self_version
                    path: 'showEntityWithLanguageAndVersion'
                  ) if elementModel.get('links')._self_version
                  options = $.extend(options, duplicate:
                    language: language
                    self_duplicate: elementModel.get('links')._self_duplicate
                    path: 'showEntityWithLanguage'
                  ) if elementModel.get('links')._self_duplicate
                  view = new FullPageFormView(options)
                  appRouter.setCurrentMainView view
              founded = true
        unless founded
          view = new TableviewCollectionView(
            elements: elements
            displayedElements: displayedElements
            title: title
            listUrl: listUrl
            el: target
          )
          appRouter.setCurrentMainView view
