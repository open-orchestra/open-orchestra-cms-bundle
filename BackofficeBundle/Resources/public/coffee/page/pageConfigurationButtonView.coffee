PageConfigurationButtonView = OrchestraView.extend(
  initialize: (options) ->
    @cid = options.cid
    @loadTemplates [
      "widgetPageConfigurationButton"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetPageConfigurationButton',
      cid: @cid
    )
    addCustomJarvisWidget(widget)
    return
)
