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
    $('#left-panel nav li.active').removeClass 'active'
    link = $('#left-panel nav li a' + selector)
    if link.length == 0
      Backbone.history.navigate('', {trigger: true})
      return false
    @afterRouteChanges(selector)
    displayLoader()
    return true
  afterRouteChanges: (selector) ->
    $('#left-panel nav li:has(a' + selector + ')').addClass 'active'
    $('#left-panel nav li.current').removeClass 'current'
    $('#left-panel nav li:has(>a' + selector + ')').addClass 'current'
    openMenu $("#left-panel nav").data('opts').speed, $("#left-panel nav").data('opts').openedSign
    document.title = $('#left-panel nav a' + selector).attr('title') or document.title
    drawBreadCrumb()

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
  route: (route, name, callBack) ->
    Backbone.Router.prototype.route.call(this, route, name, callBack);
    @addRoutePattern(name, route)
    return
  extractParameters: () ->
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
      return this._extractParameters(route, fragment)
    return {}
)
appRouter = new OrchestraBORouter
jQuery ->
  if window.location.pathname.indexOf('login') == -1
    Backbone.history.start()
  return
