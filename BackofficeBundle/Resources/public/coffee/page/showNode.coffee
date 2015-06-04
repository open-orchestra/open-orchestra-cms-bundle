showNode = (url, language, version)->
  url = url + '?language=' + language if (typeof language != 'undefined')
  url = url + '&version=' + version if (typeof version != 'undefined')
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      new NodeView(
        node: node
        domContainer: $('#content')
      )
      return
  return

showNodeForm = (parentNode) ->
  new adminFormView(
    url: parentNode.data("url")
    extendView: [ 'generateId' ]
    title: parentNode.text()
  )
  return
