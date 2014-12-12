tableViewLoad = (link, entityType, entityId, language) ->
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
              url = elementModel.get('links')._self_form
              url = url + '?language=' + language if (typeof language != 'undefined')
              $.ajax
              $.ajax
                url: url
                method: "GET"
                success: (response) ->
                  language = values.language if (typeof language == 'undefined')
                  options =
                    html: response
                    title: title
                    listUrl: listUrl
                    element: elementModel
                  )
                  options = $.extend(options, multiLanguage:
                    language_list : values.links._language_list
                    language : language
                    path: 'showEntityWithLanguage'
                  ) if values.links._language_list and values.language
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
