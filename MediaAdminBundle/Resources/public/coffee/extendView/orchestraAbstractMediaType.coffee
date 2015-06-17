extendView = extendView || {}
extendView['orchestraMediaAbstractType'] =

  clearMedia: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    inputId = '#' + target.data('input')
    previewId = '#previewImage_' + target.data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'

  openMediaModal: (modal, inputId, url, method, extend) ->
    viewClass = appConfigurationView.getConfiguration('media', 'showMediaModal')
    modalOptions =
      domContainer: modal
      input: inputId
      url: url
      galleryView: extend
    new viewClass($.extend(modalOptions,
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"))
    $.ajax
      url: url
      method: method
      success: (response) ->
        new viewClass($.extend(modalOptions,
          body: response))
      error: ->
        new viewClass($.extend(modalOptions,
          body: 'Erreur durant le chargement'))
    return

  launchMediaModal: (event) ->
    event.preventDefault()
    @method = if @options.method then @options.method else 'GET'
    target = $(event.currentTarget)
    modal = $('#' + target.data("target"), @$el)
    inputId = target.data("input")
    url = target.data("url")
    @currentLaunchModal(modal, inputId, url, @method)
