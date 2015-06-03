widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.duplicate
    new DuplicateView(
      domContainer: view.$el.find('#entity-duplicate')
      currentDuplicate: view.options.duplicate
    )
