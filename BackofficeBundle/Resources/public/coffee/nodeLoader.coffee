$(".ajax-load span").click (e) ->
  e.preventDefault()
  url = $(this).parent().data("url")
  nodeId = $(this).parent().data("node")
  self.location.hash = nodeId
  showNode(url)
  return
