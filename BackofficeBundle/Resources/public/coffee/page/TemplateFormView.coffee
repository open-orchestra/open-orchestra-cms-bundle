TemplateFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showTemplate",
        templateId: $('#oo_template_templateId', @$el).val()
      displayMenu(displayRoute)

)

jQuery ->
  appConfigurationView.setConfiguration('template', 'showOrchestraModal', TemplateFormView)
