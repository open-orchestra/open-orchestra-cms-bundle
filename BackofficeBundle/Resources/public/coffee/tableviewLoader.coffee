tableViewLoad = (link) ->
  displayedElements = link.data('displayed-elements').replace(/\s/g, '').split(",")
  title = link.text()
  listUrl = link.data('url')
  $.ajax
    url: listUrl
    method: 'GET'
    success: (response) ->
      elements = new TableviewElement
      elements.set response
      view = new TableviewCollectionView(
        elements: elements
        displayedElements: displayedElements
        title: title
        listUrl: listUrl
      )
