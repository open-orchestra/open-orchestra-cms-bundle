FolderDeleteButtonView = OrchestraView.extend(
  initialize: (options) ->
    @cid = options.cid
    @loadTemplates [
      "widgetFolderDeleteButton"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetFolderDeleteButton',
      cid: @cid
    )
    addCustomJarvisWidget(widget)
    return
)
