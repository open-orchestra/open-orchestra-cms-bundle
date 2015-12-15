ContentTypeFormView = FullPageFormView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl('listEntities', entityType: @options.entityType)
      displayMenu(undefined, displayRoute)
)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', ContentTypeFormView)
