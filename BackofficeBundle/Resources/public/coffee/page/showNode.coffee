showNode = (url, language, version)->
  url = url + '?language=' + language if (typeof language != 'undefined')
  url = url + '&version=' + version if (typeof version != 'undefined')
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      view = new NodeView(
        node: node
      )
      appRouter.setCurrentMainView(view)
      return
  return

showNodeForm = (parentNode) ->
  $(".modal-title").text parentNode.text()
  view = new adminFormView(
    url: parentNode.data("url")
    generateId: true
  )
  return
