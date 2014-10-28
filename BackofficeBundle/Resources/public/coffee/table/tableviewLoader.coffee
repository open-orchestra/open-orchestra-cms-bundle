tableViewLoad = (link, target, documentActions) ->
  if typeof target is "undefined"
    target = "#content"
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  title = link.text()
  listUrl = Backbone.history.fragment
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        elements = new TableviewElement
        elements.set response
        view = new TableviewCollectionView(
          elements: elements
          displayedElements: displayedElements
          title: title
          listUrl: listUrl
          el: target
          documentActions: documentActions
        )
        appRouter.setCurrentMainView(view)
