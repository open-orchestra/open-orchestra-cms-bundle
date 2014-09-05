var OrchestraBORouter = Backbone.Router.extend({
  
  routes: {
    'node/show/:nodeId': 'showNode',
    'node/create/:parentNodeId': 'createNode',
    'template/show/:templateId': 'showTemplate',
    'template/create': 'createTemplate',
    'translation': 'listTranslations'
  },

  initialize: function() {
  },

  showNode: function(nodeId) {
    var url = $("#nav-node-" + nodeId).data("url");
    showNode(url);
  },

  createNode: function(parentNodeId) {
    var parentNode = $("#nav-createNode-" + parentNodeId);
    this.showNodeForm(parentNode);
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

  showTemplate: function(templateId) {
    var url = $("#nav-template-" + templateId).data("url");
    showTemplate(url);
  },

  createTemplate: function() {
    var templateRoot = $("#nav-createTemplate");
    this.showNodeForm(templateRoot);
  },

  listTranslations: function() {
    var url = $("#nav-translation").data("url");
    return new TranslationView({url : url});
  }

});

var appRouter = new OrchestraBORouter();

Backbone.history.start();
