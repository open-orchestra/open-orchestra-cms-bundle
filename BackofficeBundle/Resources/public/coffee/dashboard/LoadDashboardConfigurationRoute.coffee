((router) ->
  router.route 'dashboard', 'showDashboard', ->
    @initDisplayRouteChanges()
    new DashboardView(domContainer: $('#content'))
    return

) window.appRouter
