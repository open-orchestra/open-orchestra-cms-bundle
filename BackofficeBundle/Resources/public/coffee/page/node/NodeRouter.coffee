###*
 * Router node
###
((router) ->

  ###*
   * show node
  ###
  router.route 'node/show/:nodeId', 'showNode', (nodeId) ->
    @showNodeAction(nodeId)

  ###*
   * show node with language
  ###
  router.route 'node/show/:nodeId/:language', 'showNodeWithLanguage', (nodeId, language) ->
    @showNodeAction(nodeId, language)
    return

  ###*
   * show node with language and version
  ###
  router.route 'node/show/:nodeId/:language/:version', 'showNodeWithLanguageAndVersion', (nodeId, language, version) ->
    @showNodeAction(nodeId, language, version)
    return

  ###*
   * load node and display node view
  ###
  router.showNodeAction = (nodeId, language = null, version = null)->
    @initDisplayRouteChanges '#nav-node-' + nodeId
    url = $('#nav-node-' + nodeId).data('url')
    url = url + '?language=' + language if language?
    url = url + '&version=' + version if version?
    $.ajax
      type: "GET"
      url: url
      success: (response) ->
        node = new NodeModel
        node.set response
        nodeViewClass = appConfigurationView.getConfiguration('node', 'showNode')
        new nodeViewClass(
          node: node
          domContainer: $('#content')
        )
        return
    return

) window.appRouter
