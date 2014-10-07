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
  view = new adminFormView(
    url: parentNode.data("url")
    triggers: [
      {
        event: "keyup input.alias-source"
        name: "refreshAlias"
        fct: refreshAlias
      }
      {
        event: "blur input.alias-dest"
        name: "stopRefreshAlias"
        fct: stopRefreshAlias
      }
    ]
  )
  return
