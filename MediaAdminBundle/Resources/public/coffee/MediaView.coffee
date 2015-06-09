MediaView = OrchestraView.extend(
  events:
    'click span.media-remove': 'confirmRemoveMedia'
    'click span.media-select': 'mediaSelect'
    'mouseenter': 'toggleCaption'
    'mouseleave': 'toggleCaption'

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
        @mediaClass = "media-remove"
        @mediaLogo = "fa-trash-o"
    else
      @mediaClass = "media-select"
      @mediaLogo = "fa-check-circle"
    @loadTemplates [
      'OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaView',
      media: @options.media
      mediaClass: @mediaClass
      mediaLogo: @mediaLogo
    )
    @options.domContainer.append @$el

  toggleCaption: (event) ->
    @$el.find(".caption").slideToggle(150)

  superboxOpen: ->
    listUrl = Backbone.history.fragment
    Backbone.history.navigate(listUrl + '/media/' + @options.media.id + '/edit')
    viewClass = appConfigurationView.getConfiguration('media', 'showSuperbox')
    new viewClass (@addOption(
      listUrl: listUrl
    ))

  confirmRemoveMedia: (event) ->
    smartConfirm(
      'fa-trash-o',
      $(".delete-confirm-question").text(),
      $(".delete-confirm-explanation").text(),
      callBackParams:
        mediaView: @
      yesCallback: (params) ->
        params.mediaView.removeMedia(event)
    )

  removeMedia : (event) ->
    target = $(event.target)
    $.ajax
      url: @options.media.get("links")._self_delete
      method: 'Delete'
      success: (response) ->
        target.parents(".superbox-list").remove()

  mediaSelect : (event) ->
    event.preventDefault()
    mediaModalContainer = @$el.parents(".mediaModalContainer")
    intputName = mediaModalContainer.data('input')
    $('#' + intputName).val @options.media.id
    $('#previewImage_' + intputName).attr 'src', @$el.find('.superbox-img').attr('src')
    mediaModalContainer.find('.mediaModalClose').click()
)
