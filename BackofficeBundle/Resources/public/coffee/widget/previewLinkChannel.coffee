previewLinkChannel = Backbone.Wreqr.radio.channel('preview_link')

previewLinkChannel.commands.setHandler 'render', (previewLinks) ->
  view = new PreviewLinkView(
    previewLinks: previewLinks
  )
