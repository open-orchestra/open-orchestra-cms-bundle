var OrchestraBORouter = Backbone.Router.extend({

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId': 'showNode',
    'template/show/:templateId': 'showTemplate',
    'template/create': 'createTemplate',
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

  createNode: function(parentNodeId) {
    this.showNodeForm($("#nav-createNode-" + parentNodeId));
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

  createTemplate: function() {
    var templateRoot = $("#nav-createTemplate");
    this.showNodeForm(templateRoot);
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

  showNodeForm: function(parentNode) {
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
