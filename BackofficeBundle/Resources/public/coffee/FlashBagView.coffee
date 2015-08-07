FlashBagView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'html'
      'error'
      'domContainer'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/flashBag'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/flashBag', @options)
    @options.domContainer.append @$el
    return
)
