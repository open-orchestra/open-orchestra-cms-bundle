PreviewLinkView = OrchestraView.extend(
  extendView : [ 'concurrency' ]

  initialize: (options) ->
    @previewLinks = options.previewLinks
    @widget_index = options.widget_index
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPreviewLink',
      previewLinks: @previewLinks
    )
    @$el.attr('data-widget-index', @widget_index)
    addCustomJarvisWidget(@$el)
    return
)
