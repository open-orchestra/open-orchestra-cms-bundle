((router) ->
  router.route 'template/show/:templateId', 'showTemplate', (templateId) ->
    @initDisplayRouteChanges '#nav-template-' + templateId
    showTemplate $('#nav-template-' + templateId).data('url')
    return

  router.route 'gs_template/show/:templateId', 'showGSTemplate', (templateId) ->
    @initDisplayRouteChanges '#nav-gs-template-' + templateId
    showGSTemplate $('#nav-gs-template-' + templateId).data('url')
    return
) window.appRouter
