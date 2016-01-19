PreviewLinkView = OrchestraView.extend(
  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink',
      previewLinks: @options.previewLinks
    )
    @$el.attr('data-widget-index', @options.widget_index)
    addCustomJarvisWidget(@$el, @options.domContainer)
    return
)
