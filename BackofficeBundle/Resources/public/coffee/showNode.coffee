vent = _.extend({}, Backbone.Events)
showNode = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      node = new Node
      node.set response
      switchLoaderFullPage('off')
      view = new NodeView(node: node)
      appRouter.setCurrentMainView(view)
      return
  return

showNodeForm = (parentNode) ->
  $(".modal-title").text parentNode.text()
  refreshAlias = ->
    $("input#node_alias").val $("input#node_name").val()
    return
  stopRefreshAlias = ->
    $("input#node_alias").unbind()
    return
  view = new adminFormView(url: parentNode.data("url"), triggers: [{event: 'keyup input#node_name', name: 'refreshAlias', fct : refreshAlias}, {event: 'blur input#node_alias', name: 'stopRefreshAlias', fct : stopRefreshAlias}])
  return
