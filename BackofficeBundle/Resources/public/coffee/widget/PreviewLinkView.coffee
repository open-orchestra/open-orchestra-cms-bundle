PreviewLinkView = OrchestraView.extend(
  initialize: (options) ->
    @previewLinks = options.previewLinks
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink"
    ]
    return

  render: ->
    widget = @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink',
      previewLinks: @previewLinks
    )
    addCustomJarvisWidget(widget)
    return
)
