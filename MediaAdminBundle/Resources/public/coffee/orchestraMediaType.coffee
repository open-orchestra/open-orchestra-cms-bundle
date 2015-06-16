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
    target = $(event.currentTarget)
    url = target.data("url")
    @method = if @options.method then @options.method else 'GET'
    modalOptions =
      domContainer: $('#' + target.data("target"), @$el)
      input: target.data("input")
      url: url
    viewClass = appConfigurationView.getConfiguration('media', 'showMediaModal')
    new viewClass($.extend(modalOptions, 
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"))
    $.ajax
      url: url
      method: @method
      success: (response) ->
        new viewClass($.extend(modalOptions, 
          body: response))
      error: ->
        new viewClass($.extend(modalOptions, 
          body: 'Erreur durant le chargement'))
    return

widgetChannel.bind 'ready', (view) ->
  if $("[data-prototype*='clear-media']", view.$el).length > 0
    $.extend true, view, extendView['orchestraMediaType']
    return view.delegateEvents()
  return
