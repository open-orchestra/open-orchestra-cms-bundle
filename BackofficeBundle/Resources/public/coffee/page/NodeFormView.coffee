NodeFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#node_nodeId', @$el).val()
      Backbone.history.loadUrl(displayRoute)
      displayMenu(displayRoute)

)

jQuery ->
  appConfigurationView.setConfiguration('node', 'showOrchestraModal', NodeFormView)
