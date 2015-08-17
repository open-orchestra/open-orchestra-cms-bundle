((router) ->
  router.addRoutePattern('showNode', 'node/show/:nodeId')
  router.addRoutePattern('showNodeWithLanguage', 'node/show/:nodeId/:language')
  router.addRoutePattern('showNodeWithLanguageAndVersion', 'node/show/:nodeId/:language/:version')

  router.nodeWithLanguageAndVersion = (nodeId, language, version) ->
    if selectorExist($('#nav-node-' + nodeId))
      @initDisplayRouteChanges '#nav-node-' + nodeId
      showNode $('#nav-node-' + nodeId).data('url'), language, version
    else
      Backbone.history.navigate ''
    return

  router.route 'node/show/:nodeId', 'showNode', (nodeId) ->
    @nodeWithLanguageAndVersion nodeId
    return

  router.route 'node/show/:nodeId/:language', 'showNodeWithLanguage', (nodeId, language) ->
    @nodeWithLanguageAndVersion nodeId, language
    return
  router.route 'node/show/:nodeId/:language/:version', 'showNodeWithLanguageAndVersion', (nodeId, language, version) ->
    @nodeWithLanguageAndVersion nodeId, language, version
    return
) window.appRouter
