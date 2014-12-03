GalleryView = OrchestraView.extend(
  className: 'superbox-list'

  initialize: (options) ->
    @events = []
    key = 'click .superbox-img-' + @cid
    @events[key] = 'superboxOpen'
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
      cid: @cid
    )
    this

  superboxOpen: ->
    listUrl = Backbone.history.fragment
    Backbone.history.navigate(listUrl + '/media/edit')
    superboxView = new SuperboxView (
      media: @media
      listUrl: listUrl
    )
)
