OrchestraView = Backbone.View.extend(
  loadTemplates: ->
    currentView = @
    $.each @templates, (templateName, templateData) ->
      currentView.loadTemplate(templateName, appRouter.generateUrl('loadUndescroreTemplate', {templateId: templateName}))
      return
    return

  loadTemplate: (templateName, templateFile) ->
    alert('loadTemplate ' + templateName)
    
    @templates[templateName] = false
    currentView = @
    templateLoader.loadRemoteTemplate templateName, templateFile, currentView
    return

  onTemplateLoaded: (templateName, templateData) ->
    alert('onTemplateLoaded ' + templateName)
    
    @templates[templateName] = _.template(templateData)
    
    ready = true
    $.each @templates, (templateName, templateData) ->
      ready = false if templateData is false
      return
    
    @render() if ready
    return

  renderTemplate: (templateName, parameters) ->
    alert('renderTemplate ' + 'templateName')
    @templates[templateName](parameters)
)