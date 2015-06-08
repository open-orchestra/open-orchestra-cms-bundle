extendView = extendView || {}
extendView['orchestraMediaType'] =
  events:
    'click .clear-media': 'clearMedia'
    'click .mediaModalOpen': 'openMediaModal'
  clearMedia: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    inputId = '#' + target.data('input')
    previewId = '#previewImage_' + target.data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'
  openMediaModal: (event) ->
    event.preventDefault()
    @method = if @options.method then @options.method else 'GET'
    target = $(event.currentTarget)
    modal = $('#' + target.data("target"), @$el)
    inputId = target.data("input")
    url = target.data("url")
    new MediaModalView(
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
      domContainer: modal
      input: inputId
    )
    $.ajax
      url: url
      method: @method
      success: (response) ->
        new MediaModalView(
          body: response
          domContainer: modal
          input: inputId
        )
      error: ->
        new MediaModalView(
          body: 'Erreur durant le chargement'
          domContainer: modal
          input: inputId
        )
    return

widgetChannel.bind 'ready', (view) ->
  if $("[data-prototype*='clear-media']", view.$el).length > 0
    $.extend true, view, extendView['orchestraMediaType']
    return view.delegateEvents()
  return
