((router) ->
  router.manageEntity = (entityType, entityId, language, version, sourceLanguage) ->
    @initDisplayRouteChanges '#nav-' + entityType
    entityViewLoad $('#nav-' + entityType), entityType, entityId, language, version, sourceLanguage
    return

  router.route ':entityType/list(/:page)', 'listEntities', (entityType, page) ->
    @initDisplayRouteChanges '#nav-' + entityType
    tableViewLoad $('#nav-' + entityType), entityType, page
    return

  router.route ':entityType/add', 'addEntity', (entityType) ->
    @manageEntity entityType, null, null, null, true
    return

  router.route ':entityType/edit/:entityId(/language_:language)(/version_:version)', 'showEntity', (entityType, entityId, language, version) ->
    @manageEntity entityType, entityId, language, version
    return

  router.route ':entityType/edit/:entityId/:language/source/:sourceLanguage', 'showEntityWithLanguageAndSourceLanguage', (entityType, entityId, language, sourceLanguage) ->
    @manageEntity entityType, entityId, language, undefined, sourceLanguage
    return
) window.appRouter
