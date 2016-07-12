###*
 * @namespace OpenOrchestra:Page:Node
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Node or= {}

###*
 * @class NodeFormView
###
class OpenOrchestra.Page.Node.NodeFormView extends OrchestraModalView

  ###*
   * Method call when view is ready
   * Redirect to  the template created when form is submitted and valid
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#oo_node_nodeId', @$el).val()
      refreshMenu(displayRoute)
      Backbone.history.loadUrl(Backbone.history.fragment);

jQuery ->
  appConfigurationView.setConfiguration('node', 'showOrchestraModal', OpenOrchestra.Page.Node.NodeFormView)
