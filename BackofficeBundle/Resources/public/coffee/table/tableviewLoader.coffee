tableViewLoad = (link, entityType, entityId) ->
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
        if entityId
          elements = new TableviewElement()
          elements.set response
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
                  view = new FullPageFormView(
                    html: response
                    title: title
                    listUrl: listUrl
                    element: elementModel
                  )
              founded = true
        unless founded
          elements = new TableviewElement()
          elements.set response
          view = new TableviewCollectionView(
            elements: elements
            displayedElements: displayedElements
            title: title
            listUrl: listUrl
            el: target
            entityType: entityType
          )
        appRouter.setCurrentMainView view
