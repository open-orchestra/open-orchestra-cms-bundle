GSTemplateView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  events:
    'change .grid-stack': 'sendAreaData'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
    ])
    @options.configuration = @options.template
    @options.entityType = 'gstemplate'
    @options.published = false
    appConfigurationView.setConfiguration(@options.entityType, 'addArea', GSAreaView)
    @loadTemplates [
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
    return

  sendAreaData: (event, items)->
    event.stopImmediatePropagation() if event.stopImmediatePropagation
    currentView = @
    areas = @options.template.get('areas')
    for i of items
      areaId = items[i].el.data('id')
      $.ajax
        url: areas[areaId].links._self_update
        method: 'POST'
        asynch: false
        data:
          x: items[i].x
          y: items[i].y
          width: items[i].width
          height: items[i].height
        success: (response) ->

)
