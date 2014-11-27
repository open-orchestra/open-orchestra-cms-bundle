PreviewLinkView = OrchestraView.extend(
  initialize: (options) ->
    @previewLink = options.previewLink
    @loadTemplates [
      "widgetPreviewLink"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetPreviewLink',
      previewLink: @previewLink
    )
    addCustomJarvisWidget(widget)
    return
)
