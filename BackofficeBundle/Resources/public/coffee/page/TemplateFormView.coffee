TemplateFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showTemplate",
        templateId: $('#template_templateId', @$el).val()
      Backbone.history.loadUrl(displayRoute)
      displayMenu(displayRoute)

)

jQuery ->
  appConfigurationView.setConfiguration('template', 'showOrchestraModal', TemplateFormView)
