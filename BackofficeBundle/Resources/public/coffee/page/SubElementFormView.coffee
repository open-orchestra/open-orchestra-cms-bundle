SubElementFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = Backbone.history.fragment
      Backbone.history.loadUrl(displayRoute)

)

jQuery ->
  appConfigurationView.setConfiguration('area', 'showOrchestraModal', SubElementFormView)
  appConfigurationView.setConfiguration('block', 'showOrchestraModal', SubElementFormView)
