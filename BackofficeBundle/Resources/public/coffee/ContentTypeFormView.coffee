ContentTypeFormView = FullPageFormView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl('listEntities', entityType: @options.entityType)
      displayMenu(displayRoute, true)
)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', ContentTypeFormView)
