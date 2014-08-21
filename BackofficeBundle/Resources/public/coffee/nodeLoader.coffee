$(".ajax-load span").click (e) ->
  e.preventDefault()
  url = $(this).parent().data("url")
  nodeId = $(this).parent().data("node")
  self.location.hash = nodeId
  showNode(url)
  return
$(".ajax-new").click (e) ->
  e.preventDefault()
  $('.modal-title').text $(this).text()
  $.ajax
    url: $(this).data('url')
    method: 'GET'
    success: (response) ->
      view = new adminFormView(html: response)