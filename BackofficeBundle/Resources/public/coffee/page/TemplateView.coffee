TemplateView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
    ])
    @options.configuration = @options.template
    @options.entityType = 'template'
    @options.editable = @options.template.get('editable')
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
    @addConfigurationButton()
    @addAreasToView(@options.template.get('areas'))
    return

)
