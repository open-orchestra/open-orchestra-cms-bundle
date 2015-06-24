SubElementFormView = OrchestraModalView.extend(

  onViewReady: ->
    displayMenu(displayRoute) if @options.submitted

)

jQuery ->
  appConfigurationView.setConfiguration('area', 'showOrchestraModal', SubElementFormView)
  appConfigurationView.setConfiguration('block', 'showOrchestraModal', SubElementFormView)
