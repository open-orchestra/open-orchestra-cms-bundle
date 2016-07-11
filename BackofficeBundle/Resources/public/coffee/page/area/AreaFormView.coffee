###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Area or= {}

###*
 * @class AreaFormView
###
class OpenOrchestra.Page.Area.AreaFormView extends OrchestraModalView

  ###*
   * Refresh route when form is submitted
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = Backbone.history.fragment
      Backbone.history.loadUrl(displayRoute)
