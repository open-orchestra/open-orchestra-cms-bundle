###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexFormView
###
class OpenOrchestra.AreaFlex.AreaFlexFormView extends OrchestraModalView

  ###*
   * Refresh route when form is submitted
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = Backbone.history.fragment
      Backbone.history.loadUrl(displayRoute)
