var OrchestraBORouter = Backbone.Router.extend({

  // Contains currentMainView, usefull to unbind event when view is changed
  currentMainView: null,
  
  // Declare here only routes that are not declared in this.routes.
  // Routes in this.routes will be automatically added to routePatterns at init time
  // cf this.generateRoutePatterns()
  routePatterns: {},

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId/:language/:version': 'showNodeWithLanguageAndVersion',
    'node/show/:nodeId/:language': 'showNodeWithLanguage',
    'node/show/:nodeId': 'showNode',
    'entity/show/:language': 'showEntityWithLanguage',
    'template/show/:templateId': 'showTemplate',
    ':entityType/list': 'listEntities',
    ':entityType/edit/:entityId': 'showEntity',
    'folder/:folderId/list': 'listFolder',
    'translation': 'listTranslations',
    '': 'showHome'
  },

  initialize: function() {
    this.generateRoutePatterns();
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

  showEntityWithLanguage: function(language)
  {
	var url = $("#url-entity").data("url")
    this.initDisplayRouteChanges();
    showEntity(url, language);
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
    this.showEntity(entityType);
  },

  showEntity: function(entityType, entityId)
  {
    this.initDisplayRouteChanges("#nav-" + entityType);
    tableViewLoad($("#nav-" + entityType), entityType, entityId);
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
  }
});


var appRouter = new OrchestraBORouter();

if (window.location.pathname.indexOf('login') == -1) {
    Backbone.history.start();
}
