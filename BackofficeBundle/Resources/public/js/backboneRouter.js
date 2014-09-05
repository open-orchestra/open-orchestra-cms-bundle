var OrchestraBORouter = Backbone.Router.extend({

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId': 'showNode',
    'node/create/:parentNodeId': 'createNode',
    'template/show/:templateId': 'showTemplate',
    'template/create': 'createTemplate',
    'websites/list': 'listSites',
    'content-types/list': 'listContentTypes',
    'translation': 'listTranslations'
  },

  initialize: function() {
  },

//========[ACTIONS LIST]==============================//

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

  createTemplate: function() {
    this.showNodeForm($("#nav-createTemplate"));
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
  }

});

var appRouter = new OrchestraBORouter();

Backbone.history.start();
