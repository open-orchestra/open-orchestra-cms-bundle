###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Area or= {}

###*
 * @class AreaView
###
class OpenOrchestra.Page.Area.AreaView extends OrchestraView

  extendView: ['OpenOrchestra.Page.Area.AddRow', 'OpenOrchestra.Page.Area.AddBlock']

  events:
    'click': 'triggerEditArea'
    'click .add-row': 'showFormAddRow'
    'click .add-block': 'showFormAddBlock'
    'sortstop .area-container': 'stopSortArea'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'parentAreaView'
      'domContainer'
      'toolbarContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaView"
    ]
    @options.entityType = 'area'
    OpenOrchestra.Page.Area.Channel.bind 'activateEditArea', @activateEditArea, @
    OpenOrchestra.Page.Area.Channel.bind 'activateSortableArea', @activateSortableArea, @
    OpenOrchestra.Page.Area.Channel.bind 'updateArea', @reloadArea, @
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaView', @options)
    @renderAreaBlocks($('.area-container', @$el))
    @options.domContainer.append @$el
    @setFlexWidth(@$el, @options.area.get('width')) if @options.area.get('width')
    @addSubAreas($('.area-container', @$el), @options.area.get('areas'))


  ###*
   * @param {string} areaId
  ###
  reloadArea: (areaId) ->
    if areaId == @options.area.get('area_id')
      url = @options.area.get('links')._self
      if url?
        viewContext = @
        displayLoader('.area-container', @$el)
        $.ajax
          type: "GET"
          url: url
          success: (response) ->
            area = new OpenOrchestra.Page.Area.Area
            area.set response
            viewContext.options.area = area
            $('.area-container', viewContext.$el). html('')
            viewContext.renderAreaBlocks($('.area-container', viewContext.$el))

  ###*
   * Render blocks area
  ###
  renderAreaBlocks: (container) ->
    for block in @options.area.get('blocks').models
      blockViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addBlock')
      new blockViewClass(
        domContainer: container
        block: block
        area: @options.area
      )

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
      area.css('flex-shrink', 1)

  ###*
   * activate sortable in area
   *
   * @param {string} containerAreaId
   * @param {object} areaViewSortable
  ###
  activateSortableArea: (containerAreaId, areaViewSortable) ->
    @destroySortable()
    if containerAreaId == @options.area.get('area_id')
      sortableContainer = @$el.children('.area-container')
      sortableContainer.children().addClass('blocked')
      areaViewSortable.$el.removeClass('blocked')
      sortableContainer.sortable({
        cursor: 'move'
        tolerance: 'pointer'
        cancel: '.blocked'
      })

  ###*
   * activate sortable in area
   *
   * @param {string} rowContainerAreaId
   * @param {object} rowAreaView
  ###
  activateSortableAreaColumn: (rowContainerAreaId, columnAreaView) ->
    if rowContainerAreaId == @options.area.get('area_id')
      sortableContainer = @$el.children('.area-container')
      sortableContainer.children().addClass('blocked')
      columnAreaView.$el.removeClass('blocked')
      sortableContainer.sortable({
        cancel: '.blocked'
      })

  ###*
   * Stop sortable area
  ###
  stopSortArea: (event)->
    event.stopPropagation()
    @destroySortable()
    @updateOrderChildrenAreas()

  ###*
   * Destroy sortable area
  ###
  destroySortable: () ->
    if @$el.children('.area-container').hasClass('ui-sortable')
      @$el.children('.area-container').sortable('destroy')
    OpenOrchestra.Page.Area.Channel.trigger 'disableSortableArea'
    @$el.children('.area-container').children().removeClass('blocked')
    @$el.children('.area-container').children().css('z-index', '')

  ###*
   * Update order children areas
  ###
  updateOrderChildrenAreas: () ->
    data = {}
    data.area_id = @options.area.get('area_id')
    data.areas = []
    console.log @options.area.get('areas')
    for area in @options.area.get('areas').models
      console.log area
      subArea = {}
      subArea.area_id = area.get('area_id')
      subArea.order = $('.area[data-area-id="'+area.get('area_id')+'"]', @$el).index()
      data.areas.push(subArea)
    url = @options.area.get('links')._self_move_area
    url = null
    console.log data
    if url?
      $.ajax
         url: url
         method: 'POST'
         data: JSON.stringify(data)

  ###*
   * Add sub areas
   *
   * @param {object} container jquery element
   * @param {object} areas
  ###
  addSubAreas: (container, areas) ->
    for area in areas.models
      areaViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addArea')
      new areaViewClass(
        area: area
        parentAreaView: @
        domContainer: container
        toolbarContainer: @options.toolbarContainer
      )

  ###*
   * Triggers an event to activate the edition of area
  ###
  triggerEditArea: (event) ->
    event.stopPropagation()
    if (@options.area.get('area_type') != 'row')
      OpenOrchestra.Page.Area.Channel.trigger 'activateEditArea', @options.area.get('area_id')

  ###*
    * @param {string} areaId
  ###
  activateEditArea: (areaId) ->
    if areaId == @options.area.get('area_id')
      @$el.children('.area-action').show()
      @$el.addClass('active')
      viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAreaToolbar')
      new viewClass(
        area: @options.area
        areaView: @
        domContainer: @options.toolbarContainer
      )
    else
      @$el.children('.area-action').hide()
      @$el.removeClass('active')
