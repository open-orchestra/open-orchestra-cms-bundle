GalleryView = OrchestraView.extend(
  initialize: (options) ->
    @events = @events || {}
    @options = @reduceOption(options, [
      'modal'
      'media'
      'domContainer'
    ])
    if !@options.modal
      @events['click .superbox-img'] = 'superboxOpen'
      if @options.media.get('is_deletable')
        @events['click span.media-remove'] = 'confirmRemoveMedia'
        @mediaClass = "media-remove"
        @mediaLogo = "fa-trash-o"
    else
      @mediaClass = "media-select"
      @mediaLogo = "fa-check-circle"
    @loadTemplates [
      'galleryView'
    ]
    return

  render: ->
    @setElement @renderTemplate('galleryView',
      media: @options.media
      mediaClass: @mediaClass
      mediaLogo: @mediaLogo
    )
    @options.domContainer.append @$el

  superboxOpen: ->
    listUrl = Backbone.history.fragment
    Backbone.history.navigate(listUrl + '/media/' + @options.media.id + '/edit')
    new SuperboxView (@addOption(
      listUrl: listUrl
    ))

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
      url: @options.media.get("links")._self_delete
      method: 'Delete'
      success: (response) ->
        target.parents(".superbox-list").remove()
)
