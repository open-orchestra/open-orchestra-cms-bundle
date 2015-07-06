extendView = extendView || {}
extendView['orchestraMediaAbstractType'] =

  clearMedia: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    inputId = '#' + target.data('input')
    previewId = '#previewImage_' + target.data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'

  openMediaModal: (options) ->
    viewClass = appConfigurationView.getConfiguration('media', 'showMediaModal')
    new viewClass($.extend(options,
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"))
    $.ajax
      url: options.url
      method: options.method
      success: (response) ->
        new viewClass($.extend(options,
          body: response))
      error: ->
        new viewClass($.extend(options,
          body: 'Erreur durant le chargement'))
    return

  launchMediaModal: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    {
      domContainer: $('#' + target.data("target"), @$el)
      input: target.data("input")
      url : target.data("url")
      method: if @options.method then @options.method else 'GET'
    }
