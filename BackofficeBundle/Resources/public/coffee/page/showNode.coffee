showNode = (url, language, version)->
  url = url + '?language=' + language if (typeof language != 'undefined')
  url = url + '&version=' + version if (typeof version != 'undefined')
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      nodeViewClass = appConfigurationView.getConfiguration('node', 'showNode')
      new nodeViewClass(
        node: node
        domContainer: $('#content')
      )
      return
  return

showNodeForm = (parentNode) ->
  adminFormViewClass = appConfigurationView.getConfiguration('node', 'showAdminForm')
  new adminFormViewClass(
    url: parentNode.data("url")
    extendView: [ 'generateId' ]
    title: parentNode.text()
    entityType: 'node'
  )
  return
