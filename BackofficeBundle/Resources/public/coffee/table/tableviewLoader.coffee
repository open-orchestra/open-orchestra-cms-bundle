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
          view = null
          $.each collection, (rank, values) ->
            elementModel = new TableviewModel
            elementModel.set values
            if entityId is elementModel.get('id')
              $.ajax
                url: elementModel.get('links')._self_form
                method: "GET"
                success: (response) ->
                  options =
                    html: response
                    title: title
                    listUrl: listUrl
                    element: elementModel
                  )
                  options = $.extend(options, multiLanguage:
                    language_list : values.links._language_list
                    language : values.language
                  ) if values.links._language_list and values.language
                  view = new FullPageFormView(options)
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
