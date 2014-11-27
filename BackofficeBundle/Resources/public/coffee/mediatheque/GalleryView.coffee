GalleryView = OrchestraView.extend(
  className: 'superbox-list'

  initialize: (options) ->
    @media = options.media
    @title = options.title
    @listUrl = options.listUrl
    _.bindAll this, "render"
    @loadTemplates [
      'galleryView'
    ]
    return

  render: ->
    $(@el).append @renderTemplate('galleryView',
      media: @media
    )
    this
)
