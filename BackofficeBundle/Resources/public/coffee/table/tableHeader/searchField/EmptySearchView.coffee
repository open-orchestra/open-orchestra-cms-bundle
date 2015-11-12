EmptySearchView = OrchestraView.extend(

  tagName: "td"

  initialize: (options) ->
    @options = options
    @render()
    widgetChannel.trigger 'ready', @
    return

  render: ->
    @$el.attr('data-column', @options.column)
    @options.domContainer.append(@$el)
)
