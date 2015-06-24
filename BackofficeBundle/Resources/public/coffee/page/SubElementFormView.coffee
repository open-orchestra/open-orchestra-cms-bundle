SubElementFormView = OrchestraModalView.extend(

  onViewReady: ->
    displayMenu() if @options.submitted

)

jQuery ->
  appConfigurationView.setConfiguration('area', 'showOrchestraModal', SubElementFormView)
  appConfigurationView.setConfiguration('block', 'showOrchestraModal', SubElementFormView)
