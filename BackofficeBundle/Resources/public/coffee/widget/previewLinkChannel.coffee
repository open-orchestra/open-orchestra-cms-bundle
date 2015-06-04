widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.node
    new PreviewLinkView(
      previewLinks: view.options.node.get('preview_links')
    )
