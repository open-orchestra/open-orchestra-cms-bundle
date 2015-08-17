((router) ->
  router.addRoutePattern('showDashboard', 'dashboard')

  router.route 'dashboard', 'showDashboard', ->
    @initDisplayRouteChanges()
    new DashboardView(domContainer: $('#content'))
    return

) window.appRouter
