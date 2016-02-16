###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexView
###
class OpenOrchestra.AreaFlex.AreaFlexView extends OrchestraView

  extendView: ['OpenOrchestra.AreaFlex.AddRow']

  events:
    'click': 'triggerEditArea'
    'click .add-row': 'showFormAddRow'

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
    @options.entityType = 'area-flex'
    OpenOrchestra.AreaFlex.Channel.bind 'activateEditArea', @activateEditArea, @
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexView', @options)
    @options.domContainer.append @$el
    @setFlexWidth(@$el, @options.area.get('width')) if @options.area.get('width')
    @addSubAreas($('.area-container', @$el), @options.area.get('areas'))

  ###*
   * Set width in flex property if width is a number else if flex-basis property
   *
   * @param {object} area jquery element
   * @param {string} width
  ###
  setFlexWidth: (area, width) ->
    if width.match /^[0-9]+$|^auto$/g
      area.css('flex', width)
    else
      area.css('flex-basis', width)

  ###*
   * Add sub areas
   *
   * @param {object} container jquery element
   * @param {object} areas
  ###
  addSubAreas: (container, areas) ->
    for area in areas
      areaModel = new Area
      areaModel.set area
      areaViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addAreaFlex')
      new areaViewClass(
        area: areaModel
        domContainer: container
        toolbarContainer: @options.toolbarContainer
      )

  ###*
   * Triggers an event to activate the edition of area
  ###
  triggerEditArea: (event) ->
    event.stopPropagation()
    if (@options.area.get('area_type') != 'row')
      OpenOrchestra.AreaFlex.Channel.trigger 'activateEditArea', @options.area.get('area_id')

  ###*
    * @param {string} areaId
  ###
  activateEditArea: (areaId) ->
    if areaId == @options.area.get('area_id')
      @$el.children('.area-action').show()
      @$el.addClass('active')
      viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAreaFlexToolbar')
      new viewClass(
        areaView: @
        domContainer: @options.toolbarContainer
      )
    else
      @$el.children('.area-action').hide()
      @$el.removeClass('active')
