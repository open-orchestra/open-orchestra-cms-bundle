###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Block or= {}

###*
 * @class AreaView
###
class OpenOrchestra.Page.Block.BlockView extends OrchestraView

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'block'
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockView"
    ]
    @options.entityType = 'block'
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockView', @options)
    @options.domContainer.append @$el
