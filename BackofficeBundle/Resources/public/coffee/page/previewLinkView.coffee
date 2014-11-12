PreviewLinkView = Backbone.View.extend(
  tagName: "div"
  el: '#preview'
  initialize: (options) ->
    @previewLink = options.previewLink
    @preview = _.template($("#previewLink").html())
    return
  render: ->
    $(@el).append @preview(
      previewLink: @previewLink
    )
    return
)
