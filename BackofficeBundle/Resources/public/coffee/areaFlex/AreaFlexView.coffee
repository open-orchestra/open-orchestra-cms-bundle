###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexView
###
class OpenOrchestra.AreaFlex.AreaFlexView extends OrchestraView

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexView"
    ]
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexView', @options)
    @options.domContainer.append @$el

