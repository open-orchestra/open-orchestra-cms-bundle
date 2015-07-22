FieldOptionDefaultValueView = OrchestraModalView.extend(

  initialize: (options) ->
    @options = @reduceOption(options, [
      'html'
    ])
    return

  render: ->
    @.$el.html(@options.html)
    return @

)
