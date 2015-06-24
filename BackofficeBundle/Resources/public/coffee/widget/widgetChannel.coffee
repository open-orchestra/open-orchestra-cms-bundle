widgetChannel = new (Backbone.Wreqr.EventAggregator)

widgetChannel.bind 'ready', (view) ->
  view.onOrchestraViewReady()
  view.onViewReady()
