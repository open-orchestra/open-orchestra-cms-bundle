NodeFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#oo_node_nodeId', @$el).val()
      Backbone.history.loadUrl(displayRoute)
      displayMenu(displayRoute)
    if !@options.disabled || @options.disabled == undefined
      $('#oo_node_templateSelection').append $('#s2id_oo_node_nodeSource').parent().parent()
      $('#oo_node_templateSelection').append $('#oo_node_templateId').parent().parent()
    if !$.trim($('#oo_node_templateSelection').html()).length
      $('#oo_node_templateSelection').parent().parent().hide()
)

jQuery ->
  appConfigurationView.setConfiguration('node', 'showOrchestraModal', NodeFormView)
