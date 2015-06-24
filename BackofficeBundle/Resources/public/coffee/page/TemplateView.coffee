TemplateView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
    ])
    @options.configuration = @options.template
    @options.entityType = 'template'
    @options.published = false
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/templateView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/templateView',
      template: @options.template
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton('template')
    @addAreasToView(@options.template.get('areas'))
    return

)
