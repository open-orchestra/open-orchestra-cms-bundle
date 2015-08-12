GSTemplateView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  events:
    'change .grid-stack': 'changeAreaData'
    'add .grid-stack': 'addAreaData'
    'delete .grid-stack': 'deleteAreaData'
    'click .add-grid-stack': 'addArea'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
      'areaButtonVisible'
    ])
    @options.configuration = @options.template
    @options.entityType = 'gstemplate'
    @options.editable = false
    appConfigurationView.setConfiguration(@options.entityType, 'addArea', GSAreaView)
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/gsAreaView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/gsTemplateView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/gsTemplateView',
      template: @options.template
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton()
    @addAreasToView(@options.template.get('areas'))
    @showAreas() if @options.areaButtonVisible
    return

  sendAreaData: (event, refresh)->
    event.stopImmediatePropagation()
    areaButtonVisible = $('.show-areas', @$el).is(':hidden')
    areas = {}
    items = $.makeArray($('.grid-stack .grid-stack-item:visible'))
    rank = -1
    $.each(items, (key, item) ->
      item = $(item).data('_gridstack_node');
      if item.el.data('id')
        rank = item.el.data('id').replace(/^area-(\d+)$/, '$1');
    )
    $.each(items, (key, item) ->
      item = $(item).data('_gridstack_node');
      id = item.el.data('id')
      if !id
        rank++
        id = 'area-' + rank
      areas[id] = 
      {
        x: item.x
        y: item.y
        width: item.width
        height: item.height
      }
    )
    $.ajaxSetup().abortXhr()
    $.ajax
      url: @options.template.get('links')._self_update_areas
      method: 'POST'
      data:
        areas : areas
      success: (response) ->
        if refresh
          template = new TemplateModel
          template.set response
          templateViewClass = appConfigurationView.getConfiguration('GStemplate', 'showGSTemplate')
          new templateViewClass(
            template: template
            domContainer: $('#content')
            areaButtonVisible: areaButtonVisible
          )

  changeAreaData: (event, items)->
    @sendAreaData(event, false)

  deleteAreaData: (event)->
    @sendAreaData(event, false)

  addAreaData: (event, items)->
    @sendAreaData(event, true)

  addArea: (event)->
    grid = $('.grid-stack', @$el).data('gridstack')
    grid.add_widget '<div class="grid-stack-item"><div class="grid-stack-item-content"><h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1></div></div>', 0, 0, 1, 1, true
    grid.container.trigger('add')
)
