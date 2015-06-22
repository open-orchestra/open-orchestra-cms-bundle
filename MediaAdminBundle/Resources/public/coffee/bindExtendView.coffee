widgetChannel.bind 'ready', (view) ->
  if $('[data-prototype*=\'mediaModalOpen\']', view.$el).length > 0 or $('.mediaModalOpen', view.$el).length > 0
    $.extend true, view, extendView['orchestraMediaAbstractType'], extendView['orchestraMediaType']
    view.delegateEvents()
  return
