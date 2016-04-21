NodeFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#oo_node_nodeId', @$el).val()
      refreshMenu(displayRoute)
      Backbone.history.loadUrl(Backbone.history.fragment);
)

jQuery ->
  appConfigurationView.setConfiguration('node', 'showOrchestraModal', NodeFormView)
