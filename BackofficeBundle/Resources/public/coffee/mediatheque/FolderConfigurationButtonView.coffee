FolderConfigurationButtonView = OrchestraView.extend(
  initialize: (options) ->
    @cid = options.cid
    @loadTemplates [
      "widgetFolderConfigurationButton"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetFolderConfigurationButton',
      cid: @cid
    )
    addCustomJarvisWidget(widget)
    return
)
