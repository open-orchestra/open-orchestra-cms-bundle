
$(".ajax-load").click (e) ->
  e.preventDefault()
  url = $(this).data("url")
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      nodeView = new NodeView(node: node)
      return
  return
