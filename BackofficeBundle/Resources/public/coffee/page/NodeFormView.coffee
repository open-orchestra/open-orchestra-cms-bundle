NodeFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#oo_node_nodeId', @$el).val()
      displayMenu(displayRoute)
)

jQuery ->
  appConfigurationView.setConfiguration('node', 'showOrchestraModal', NodeFormView)
