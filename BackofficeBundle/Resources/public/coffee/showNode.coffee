showNode = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      switchLoaderFullPage('off')
      view = new NodeView(node: node)
      appRouter.setCurrentMainView(view)
      return
  return

showNodeForm = (parentNode) ->
  $(".modal-title").text parentNode.text()
  view = new adminFormView(url: parentNode.data("url"))
  return
