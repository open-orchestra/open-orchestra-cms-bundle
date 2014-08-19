$(".ajax-load span").click (e) ->
  e.preventDefault()
  url = $(this).parent().data("url")
  nodeId = $(this).parent().data("node")
  self.location.hash = nodeId
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      view = new NodeView(node: node)
      return
  return
