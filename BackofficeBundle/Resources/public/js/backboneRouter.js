var OrchestraBORouter = Backbone.Router.extend({

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId': 'showNode',
    'template/show/:templateId': 'showTemplate',
    'contents/list/:contentTypeId': 'listContents',
    'websites/list': 'listSites',
    'content-types/list': 'listContentTypes',
    'translation': 'listTranslations',
    '': 'showHome'
  },

  initialize: function() {
  },

//========[ACTIONS LIST]==============================//

  showHome: function() {
    drawBreadCrumb();
  },

  showNode: function(nodeId) {
  this.initDisplayRouteChanges();
    showNode($("#nav-node-" + nodeId).data("url"));
  },

  showTemplate: function(templateId) {
    this.initDisplayRouteChanges();
    showTemplate($("#nav-template-" + templateId).data("url"));
  },

  listContents: function(contentTypeId) {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-contents-" + contentTypeId));
  },

  listSites: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-websites"));
  },

  listContentTypes: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-contentTypes"));
  },

  listTranslations: function() {
    drawBreadCrumb();
    return new TranslationView(
      {url : $("#nav-translation").data("url")}
    );
  },

//========[INTERNAL FUNCTIONS]========================//

  initDisplayRouteChanges: function() {
    var url = '#' + Backbone.history.fragment;
    $('nav li.active').removeClass("active");
    $('nav li:has(a[href="' + url + '"])').addClass("active");
    
    var title = ($('nav a[href="' + url + '"]').attr('title'))
    document.title = (title || document.title);
    
    drawBreadCrumb();
    displayLoader();
  },

});

var appRouter = new OrchestraBORouter();

Backbone.history.start();
