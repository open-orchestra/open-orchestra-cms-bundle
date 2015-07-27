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
    results = {}
    items = $.makeArray(items)
    $.each(items, (key, item) ->
      results[item.el.data('id')] = 
      {
        x: item.x
        y: item.y
        width: item.width
        height: item.height
      }
    )
    $.ajax
      url: @options.template.get('links')._self_update_areas
      method: 'POST'
      data:
        areas : results
      success: (response) ->
)
