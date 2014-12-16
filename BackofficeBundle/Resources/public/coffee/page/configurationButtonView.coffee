ConfigurationButtonView = OrchestraView.extend(
  initialize: (options) ->
    @cid = options.cid
    @loadTemplates [
      "widgetConfiguration"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetConfiguration',
      cid: @cid
    )
    addCustomJarvisWidget(widget)
    return
)
