showNode = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      view = new NodeView(node: node)
      return
  return

showNodeForm = (parentNode) ->
  $(".modal-title").text parentNode.text()
  displayLoader('.modal-body')
  $("#OrchestraBOModal").modal "show"
  $.ajax
    url: parentNode.data("url")
    method: "GET"
    success: (response) ->
      view = new adminFormView(html: response)
  return
