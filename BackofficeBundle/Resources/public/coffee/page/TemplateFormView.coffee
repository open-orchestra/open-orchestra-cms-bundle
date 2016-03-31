TemplateFormView = OrchestraModalView.extend(

  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showTemplate",
        templateId: $('#oo_template_templateId', @$el).val()
      refreshMenu(displayRoute)
      Backbone.history.loadUrl(Backbone.history.fragment);

)

jQuery ->
  appConfigurationView.setConfiguration('template', 'showOrchestraModal', TemplateFormView)
