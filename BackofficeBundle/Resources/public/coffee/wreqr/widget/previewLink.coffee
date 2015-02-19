PreviewLinkChannel = Backbone.Wreqr.radio.channel('preview_link')

PreviewLinkChannel.commands.setHandler 'render', (previewLinks) ->
  view = new PreviewLinkView(
    previewLinks: previewLinks
  )
