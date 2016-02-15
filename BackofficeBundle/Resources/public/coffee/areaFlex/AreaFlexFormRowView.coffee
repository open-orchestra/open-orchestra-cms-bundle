###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexView
###
class OpenOrchestra.AreaFlex.AreaFlexFormRowView extends OrchestraModalView

  events:
    'click ul.list-inline li': 'selectOptionLayout'

  ###*
   * @param {object} event
  ###
  selectOptionLayout: (event) ->
    layout = $(event.target).attr('data-layout')
    input = $('#area_flex_columnLayout_layout', @$el)
    if (input.length > 0)
      input.val(layout)

  ###*
   * Refresh route when form is submitted
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = Backbone.history.fragment
      Backbone.history.loadUrl(displayRoute)
