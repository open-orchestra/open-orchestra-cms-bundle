$('.ajax-tableview-load').click (e) ->
  e.preventDefault()
  displayedElements = $(this).data('displayed-elements').replace(/\s/g, '').split(",")
  $.ajax
    url: $(this).data('url')
    method: 'GET'
    success: (response) ->
      elements = new TableviewElement
      elements.set response
      view = new TableviewCollectionView(
        elements: elements
        displayedElements: displayedElements
      )