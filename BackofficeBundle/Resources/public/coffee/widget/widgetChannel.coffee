widgetChannel = new (Backbone.Wreqr.EventAggregator)

widgetChannel.bind 'ready', (view) ->
  view.onOrchestraViewReady()
  view.onViewReady()


widgetChannel.bind 'element-created', (view) ->
  view.onElementCreated()

widgetChannel.bind 'form-error', (view) ->
  view.onFormError()
