PreviewLinkView = Backbone.View.extend(
  initialize: (options) ->
    @previewLink = options.previewLink
    @preview = _.template($("#previewLink").html())
    return
  render: ->
    widget = @preview(
      previewLink: @previewLink
    )
    addCustomJarvisWidget(widget)
    return
)
