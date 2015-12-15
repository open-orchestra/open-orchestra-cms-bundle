ContentTypeFormView = FullPageFormView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl('listEntities', entityType: @options.entityType)
      Backbone.history.loadUrl(displayRoute)
      displayMenu(displayRoute)
)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', ContentTypeFormView)
