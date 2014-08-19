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

$("i.ajax-delete").click (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  if confirm(confirm_text)
    $.ajax
      type: "DELETE"
      url: url
      success: (response) ->
        return
    $(this).parent().parent().hide()
    return
  return
