
$(".ajax-load").click (e) ->
  e.preventDefault()
  url = $(this).data("url")
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node response
      $('#content').html(node.printHtml())
      return
  return
