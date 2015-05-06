previewLinkChannel = Backbone.Wreqr.radio.channel('preview_link')

previewLinkChannel.commands.setHandler 'render', (previewLinks) ->
  new PreviewLinkView(
    previewLinks: previewLinks
  )
