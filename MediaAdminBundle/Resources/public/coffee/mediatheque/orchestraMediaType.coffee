extendView = extendView || {}
extendView['orchestraMediaType'] =
  events: 'click .clear-media': 'clearMedia'
  clearMedia: (event) ->
    event.preventDefault()
    inputId = '#' + $(event.currentTarget).data('input')
    previewId = '#previewImage_' + $(event.currentTarget).data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'
widgetChannel.bind 'loaded', (view) ->
  if $("[data-prototype*='clear-media']", view.$el).length > 0
    $.extend true, view, extendView['orchestraMediaType']
    return view.delegateEvents()
  return
