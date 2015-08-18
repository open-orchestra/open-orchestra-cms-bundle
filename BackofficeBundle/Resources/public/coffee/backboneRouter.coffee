OrchestraBORouter = Backbone.Router.extend(
  routePatterns: {}
  routes: {
    '*path': 'showHome'
  }
  initialize: ->
    @generateRoutePatterns()
    return
  showHome: ->
    @navigate 'dashboard', true
    return
  addRoutePattern: (routeName, routePattern) ->
    @routePatterns[routeName] = routePattern
    return
  generateRoutePatterns: ->
    currentRouter = this
    for routePattern, routeName in @routes
      currentRouter.addRoutePattern routeName, routePattern
      return
    return
  initDisplayRouteChanges: (selector) ->
    selector = if selector == undefined then '[href="#' + Backbone.history.fragment + '"]' else selector
    $('nav li.active').removeClass 'active'
    $('nav li:has(a' + selector + ')').addClass 'active'
    openMenu menu_speed, openedSign
    document.title = $('nav a' + selector).attr('title') or document.title
    $.ajaxSetup().abortXhr()
    drawBreadCrumb()
    displayLoader()
    return
  generateUrl: (routeName, paramsObject) ->
    optionalParam = /\(([^)]*):([^)]*)\)/g
    namedParam = /():([^/]*)/g
    route = @routePatterns[routeName]

    replaceFunction = ->
      key = arguments[2]
      if paramsObject[key] then arguments[1] + paramsObject[key] else ''

    if route?
      route = route.replace(optionalParam, replaceFunction).replace(namedParam, replaceFunction)
    else
      alert 'Error, route name is unknown'
      return false
    return route
  addParametersToRoute: (options) ->
    Router = this
    fragment = Backbone.history.fragment
    routes = _.pairs(Router.routePatterns)
    route = null
    matched = _.find(routes, (handler) ->
      return false if handler[0] == 'showHome'
      route = if _.isRegExp(handler[1]) then handler[1] else Router._routeToRegExp(handler[1])
      route.test fragment
    )
    if matched
      paramsKeys = _.compact(Router._extractParameters(route, matched[1]))
      for i of paramsKeys
        paramsKeys[i] = paramsKeys[i].substring(1).replace(/\)|\(/g, '')
      paramsObject = _.object(paramsKeys, _.compact(Router._extractParameters(route, fragment)))
      paramsObject = _.extend(paramsObject, options)
      return paramsObject
    {}
)
appRouter = new OrchestraBORouter
jQuery ->
  if window.location.pathname.indexOf('login') == -1
    Backbone.history.start()
  return
