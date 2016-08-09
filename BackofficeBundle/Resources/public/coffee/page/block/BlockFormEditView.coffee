###*
 * @namespace OpenOrchestra:Page:Block
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Block or= {}

###*
 * @class BlockFormEditView
###
class OpenOrchestra.Page.Block.BlockFormEditView extends OrchestraModalView
  onViewReady: ->
    if @options.submitted
      displayRoute = Backbone.history.fragment
      Backbone.history.loadUrl(displayRoute)

jQuery ->
  appConfigurationView.setConfiguration('block', 'showOrchestraModal', OpenOrchestra.Page.Block.BlockFormEditView)
