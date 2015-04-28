TemplateView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'extendView'
    ])
    @options.pageConfiguration = @options.template
    @loadTemplates [
      "templateView"
    ]
    return

  render: ->
    $(@el).html @renderTemplate('templateView',
      template: @options.template
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton()
    for area of @options.template.get('areas')
      @addAreaToView(@options.template.get('areas')[area])
    return

  addAreaToView: (area) ->
    domContainer = @$el.find('div[role="container"] > div > .ui-model-areas')
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement,
      domContainer: domContainer
      viewContainer: @
    )
    domContainer.addClass (if @options.template.get("bo_direction") is "h" then "bo-row" else "bo-column")
    $(".ui-model-areas", @$el).each ->
      refreshUl $(this)
    return
)
