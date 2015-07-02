FolderFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayMenu()

)

jQuery ->
  appConfigurationView.setConfiguration('folder', 'showOrchestraModal', FolderFormView)
