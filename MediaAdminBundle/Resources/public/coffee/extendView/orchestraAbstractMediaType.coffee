extendView = extendView || {}
extendView['orchestraMediaAbstractType'] =

  clearMedia: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    inputId = '#' + target.data('input')
    previewId = '#previewImage_' + target.data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'

  openMediaModal: (modal, inputId, url, method, mediaModalView = "showMediaModal") ->
    viewClass = appConfigurationView.getConfiguration('media', mediaModalView)
    new viewClass(
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
      domContainer: modal
      input: inputId
    )
    $.ajax
      url: url
      method: method
      success: (response) ->
        new viewClass(
          body: response
          domContainer: modal
          input: inputId
        )
      error: ->
        new viewClass(
          body: 'Erreur durant le chargement'
          domContainer: modal
          input: inputId
        )
    return
