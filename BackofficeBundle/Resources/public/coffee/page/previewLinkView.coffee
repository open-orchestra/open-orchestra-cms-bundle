PreviewLinkView = OrchestraView.extend(
  initialize: (options) ->
    @previewLinks = options.previewLinks
    @loadTemplates [
      "widgetPreviewLink"
    ]
    return

  render: ->
    widget = @renderTemplate('widgetPreviewLink',
      previewLinks: @previewLinks
    )
    addCustomJarvisWidget(widget)
    return
)
