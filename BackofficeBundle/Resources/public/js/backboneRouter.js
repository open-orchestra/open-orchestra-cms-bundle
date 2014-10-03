var OrchestraBORouter = Backbone.Router.extend({

//========[ROUTES LIST]===============================//

  routes: {
    'node/show/:nodeId': 'showNode',
    'template/show/:templateId': 'showTemplate',
    'contents/list/:contentTypeId': 'listContents',
    'websites/list': 'listSites',
    'themes/list': 'listThemes',
    'status/list': 'listStatus',
    'user/list': 'listUser',
    'content-types/list': 'listContentTypes',
    ':folderId/list': 'listFolder',
    ':list/list/edit': 'redirectToList',
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

  listFolder: function(folderId) {
      this.initDisplayRouteChanges();
      tableViewLoad($('#' + folderId));
  },

  listContents: function(contentTypeId) {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-contents-" + contentTypeId));
  },

  listSites: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-websites"));
  },

  listThemes: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-themes"));
  },

  listStatus: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-status"));
  },

  listUser: function() {
    this.initDisplayRouteChanges();
    tableViewLoad($("#nav-user"));
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

  redirectToList: function(list) {
      Backbone.history.navigate(list + '/list', true);
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

});

var appRouter = new OrchestraBORouter();

if (window.location.pathname.indexOf('login') == -1) {
    Backbone.history.start();
}
