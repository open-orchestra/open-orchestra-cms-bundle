
$(".ajax-load").click (e) ->
  e.preventDefault()
  url = $(this).data("url")
  nodeId = $(this).data("node")
  self.location.hash = nodeId
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      nodeView = new NodeView(node: node)
      return
  return
