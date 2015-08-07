DisplayApiErrorView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'errors'
      'domContainer'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/apiError'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/apiError', @options)
    @options.domContainer.append @$el
    return
)
