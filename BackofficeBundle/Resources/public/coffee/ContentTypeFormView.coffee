ContentTypeFormView = FullPageFormView.extend(

  onViewReady: ->
    displayMenu() if @options.submitted

)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', ContentTypeFormView)
