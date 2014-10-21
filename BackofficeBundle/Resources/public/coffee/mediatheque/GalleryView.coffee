GalleryView = Backbone.View.extend(
  className: 'superbox-list'
  initialize: (options) ->
    @media = options.media
    @title = options.title
    @listUrl = options.listUrl
    _.bindAll this, "render"
    @elementTemplate = _.template($('#galleryView').html())
    return
  render: ->
    $(@el).html @elementTemplate(
      media: @media
    )
    this
)
