var OrchestraBORouter = Backbone.Router.extend({

  // Contains currentMainView, usefull to unbind event when view is changed
  currentMainView: null,
  
  // Declare here only routes that are not declared in this.routes.
  // Routes in this.routes will be automatically added to routePatterns at init time
  // cf this.generateRoutePatterns()
  routePatterns: {},
  keysPrint: {},

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId/:language/:version': 'showNodeWithLanguageAndVersion',
    'node/show/:nodeId/:language': 'showNodeWithLanguage',
    'node/show/:nodeId': 'showNode',
    'template/show/:templateId': 'showTemplate',
    ':entityType/list': 'listEntities',
    ':entityType/edit/:entityId': 'showEntity',
    ':entityType/edit/:entityId/:language': 'showEntityWithLanguage',
    'folder/:folderId/list': 'listFolder',
    'translation': 'listTranslations',
    '': 'showHome'
  },

  initialize: function() {
    this.generateRoutePatterns();
    this.generateKeysPrint();
  },

//========[ACTIONS LIST]==============================//

  showHome: function()
  {
    drawBreadCrumb();
  },

  showNode: function(nodeId)
  {
    this.showNodeWithLanguageAndVersion(nodeId);
  },

  showNodeWithLanguage: function(nodeId, language)
  {
    this.showNodeWithLanguageAndVersion(nodeId, language);
  },

  showNodeWithLanguageAndVersion: function(nodeId, language, version)
  {
      if (selectorExist($("#nav-node-" + nodeId))) {
          this.initDisplayRouteChanges("#nav-node-" + nodeId);
          showNode($("#nav-node-" + nodeId).data("url"), language, version);
      } else {
          Backbone.history.navigate("");
      }
  },

  showTemplate: function(templateId)
  {
    this.initDisplayRouteChanges();
    showTemplate($("#nav-template-" + templateId).data("url"));
  },

  listFolder: function(folderId)
  {
    this.initDisplayRouteChanges();
    GalleryLoad($('#' + folderId));
  },

  listEntities: function(entityType)
  {
    this.manageEntity(entityType);
  },

  showEntity: function(entityType, entityId)
  {
    this.manageEntity(entityType, entityId);
  },

  showEntityWithLanguage: function(entityType, entityId, language)
  {
    this.manageEntity(entityType, entityId, language);
  },
  
  manageEntity: function(entityType, entityId, language)
  {
    this.initDisplayRouteChanges("#nav-" + entityType);
    tableViewLoad($("#nav-" + entityType), entityType, entityId, language);
  },

  listTranslations: function()
  {
    drawBreadCrumb();
    var view = new TranslationView(
      {url : $("#nav-translation").data("url")}
    );
    this.setCurrentMainView(view);
    return view;
  },

//========[INTERNAL FUNCTIONS]========================//

  addRoutePattern: function(routeName, routePattern)
  {
    this.routePatterns[routeName] = routePattern;
  },

  generateRoutePatterns: function()
  {
    var currentRouter = this;
    $.each(this.routes, function(routePattern, routeName) {
      currentRouter.addRoutePattern(routeName, routePattern);
    });
    this.addRoutePattern(
      'loadUnderscoreTemplate',
      $('#contextual-informations').data('templateUrlPattern')
    );
  },

  generateKeysPrint: function()
  {
    var Router = this,
        routes = _.pairs(Router.routes),
        keys = null;
    $.each(routes, function(key, value) {
      keys = Router._extractParameters(Router._routeToRegExp(value[0]), value[0]);
      keys = _.compact(keys);
      keys = keys.sort();
      Router.keysPrint[value[1]] = keys;
    });
  },

  initDisplayRouteChanges: function(selector)
  {
    $('nav li.active').removeClass("active");
      if (selector == undefined) {
          var url = '#' + Backbone.history.fragment;
          $('nav li:has(a[href="' + url + '"])').addClass("active");
          var title = ($('nav a[href="' + url + '"]').attr('title'));
          document.title = (title || document.title);
      } else {
          $('nav li:has(a' + selector + ')').addClass("active");
          var title = ($('nav a' + selector).attr('title'));
          document.title = (title || document.title);
      }

    drawBreadCrumb();

    this.removeCurrentMainView();

    displayLoader();
  },

  showNodeForm: function(parentNode)
  {
    $('.modal-title').text(parentNode.text());
    $.ajax({
      url: parentNode.data('url'),
      method: 'GET',
      success: function(response) {
        $('#OrchestraBOModal').modal('show');
        var view;
        return view = new adminFormView({
          html: response
        });
      }
    }); 
  },

  setCurrentMainView: function(view)
  {
    this.currentMainView = view;
  },

  removeCurrentMainView: function()
  {
    if (this.currentMainView) {
      this.currentMainView.remove();
      this.setCurrentMainView(null);
      $('#main').append('<div id="content" />');
    }
  },

  generateUrl: function(routeName, paramsObject)
  {
    var route = this.routePatterns[routeName];
    if (typeof route !== "undefined") {
      $.each(paramsObject, function(paramName, paramValue) {
        route = route.replace(':' + paramName, paramValue);
      });
    } else {
      alert('Error, route name is unknown');
      return false;
    }

    return route;
  },
  
  addParametersToRoute: function(options)
  {
    var Router = this,
        fragment = Backbone.history.fragment,
        routes = _.pairs(Router.routes),
        keysPrint = _.pairs(Router.keysPrint),
        route = null, newroute = null, matched, paramsObject = null, paramsKeys = null;
    matched = _.find(routes, function(handler) {
      route = _.isRegExp(handler[0]) ? handler[0] : Router._routeToRegExp(handler[0]);
      return route.test(fragment);
    });
    
    if(matched) {
      paramsObject = _.object(_.compact(Router._extractParameters(route, matched[0])), _.compact(Router._extractParameters(route, fragment)))
      paramsObject = _.extend(paramsObject, options);
      paramsKeys = _.keys(paramsObject).sort();
      matched = _.find(keysPrint, function(handler) {
        newroute = Router.routePatterns[handler[0]]
        return _.isEqual(paramsKeys, handler[1]);
      });
      if(matched) {
        $.each(paramsObject, function(paramName, paramValue) {
          newroute = newroute.replace(paramName, paramValue);
        });
        Backbone.history.navigate(newroute, {trigger: true})
      }
    }
    if(!matched){
      Backbone.history.navigate('', {trigger: true})
    }
  }
});


var appRouter = new OrchestraBORouter();

if (window.location.pathname.indexOf('login') == -1) {
    Backbone.history.start();
}
