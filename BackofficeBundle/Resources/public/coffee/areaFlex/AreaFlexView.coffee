###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexView
###
class OpenOrchestra.AreaFlex.AreaFlexView extends OrchestraView

  events:
    'click': 'triggerEditArea'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'domContainer'
      'toolbarContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexView"
    ]
    OpenOrchestra.AreaFlex.Channel.bind 'activateEditArea', @activateEditArea, @
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexView', @options)
    @options.domContainer.append @$el

  ###*
   * Triggers an event to activate the edition of area
  ###
  triggerEditArea: ->
    OpenOrchestra.AreaFlex.Channel.trigger 'activateEditArea', @options.area.get('area_id')

  ###*
    * @param {string} areaId
  ###
  activateEditArea: (areaId) ->
    if areaId == @options.area.get('area_id')
      $('.area-action', @$el).show()
      @$el.addClass('active')
      new OpenOrchestra.AreaFlex.AreaFlexToolbarView(
        area: @options.area
        domContainer: @options.toolbarContainer
      )
    else
      $('.area-action', @$el).hide()
      @$el.removeClass('active')

