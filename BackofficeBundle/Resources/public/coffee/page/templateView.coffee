TemplateView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'extendView'
      'domContainer'
    ])
    @options.pageConfiguration = @options.template
    @loadTemplates [
      "templateView"
    ]
    return

  render: ->
    @setElement @renderTemplate('templateView',
      template: @options.template
    )
    @options.domContainer.remove('#content')
    @options.domContainer.append @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton()
    for area of @options.template.get('areas')
      @addAreaToView(@options.template.get('areas')[area])
    return

)
