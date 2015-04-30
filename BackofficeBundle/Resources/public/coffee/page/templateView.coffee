TemplateView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
    ])
    @options.configuration = @options.template
    @options.published = false
    @loadTemplates [
      "templateView"
    ]
    return

  render: ->
    @setElement @renderTemplate('templateView',
      template: @options.template
    )
    @options.domContainer.find('#content').remove()
    @options.domContainer.append @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton()
    @addAreasToView(@options.template.get('areas'))
    return

)
