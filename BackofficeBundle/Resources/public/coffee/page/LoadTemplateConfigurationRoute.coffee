((router) ->
  router.route 'template/show/:templateId', 'showTemplate', (templateId) ->
    @initDisplayRouteChanges '#nav-template-' + templateId
    showTemplate $('#nav-template-' + templateId).data('url')
    return
) window.appRouter
