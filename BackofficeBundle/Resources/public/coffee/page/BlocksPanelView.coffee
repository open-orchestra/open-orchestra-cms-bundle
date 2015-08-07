BlocksPanelView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'blocks'
      'domContainer'
    ])
    @loadTemplates [
        "OpenOrchestraBackofficeBundle:BackOffice:Underscore/blocksPanel"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/blocksPanel', @options)
    @options.domContainer.append @$el
    Backbone.Wreqr.radio.commands.execute 'viewport', 'init', @options.domContainer
    $(window).resize ->
      Backbone.Wreqr.radio.commands.execute 'viewport', 'init'
      return
    $(window).add('div[role="content"]').scroll ->
      Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
      return
    return

)
