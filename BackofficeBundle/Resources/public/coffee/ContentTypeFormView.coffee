ContentTypeFormView = FullPageFormView.extend(

  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
    ]
    displayMenu() if options.submitted
    return
)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', ContentTypeFormView)
