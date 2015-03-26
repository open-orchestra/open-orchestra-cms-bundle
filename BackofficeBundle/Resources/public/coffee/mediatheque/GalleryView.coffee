GalleryView = OrchestraView.extend(
  className: 'superbox-list'

  initialize: (options) ->
    @events = {}
    @media = options.media
    @title = options.title
    @listUrl = options.listUrl
    if options.target == '#content'
      key = 'click .superbox-img-' + @cid
      @events[key] = 'superboxOpen'
      if @media.get('is_deletable')
        @mediaClass = "media-remove-" + @cid
        @mediaLogo = "fa-trash-o"
        media = "click span.media-remove-" + @cid
        @events[media] = 'confirmRemoveMedia'
    else
      @mediaClass = "media-select"
      @mediaLogo = "fa-check-circle"
    _.bindAll this, "render"
    @loadTemplates [
      'galleryView'
    ]
    return

  render: ->
    $(@el).append @renderTemplate('galleryView',
      media: @media
      cid: @cid
      mediaClass: @mediaClass
      mediaLogo: @mediaLogo
    )
    this

  superboxOpen: ->
    listUrl = Backbone.history.fragment
    Backbone.history.navigate(listUrl + '/media/' + @media.id + '/edit')
    superboxView = new SuperboxView (
      media: @media
      listUrl: listUrl
    )

  confirmRemoveMedia: (event) ->
    smartConfirm(
      'fa-trash-o',
      $(".delete-confirm-question").text(),
      $(".delete-confirm-explanation").text(),
      callBackParams:
        galleryView: @
      yesCallback: (params) ->
        params.galleryView.removeMedia(event)
    )

  removeMedia : (event) ->
    target = $(event.target)
    $.ajax
      url: @media.get("links")._self_delete
      method: 'Delete'
      success: (response) ->
        target.parents(".superbox-list").remove()
)
