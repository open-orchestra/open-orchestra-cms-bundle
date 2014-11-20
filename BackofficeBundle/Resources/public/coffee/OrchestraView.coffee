OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    $.each templates, (index, templateName) ->
      currentView.compiledTemplates[templateName] = false
      return
    $.each templates, (index, templateName) ->
      currentView.loadTemplate(templateName, appRouter.generateUrl('loadUndescroreTemplate', {templateId: templateName}))
      return
    return

  loadTemplate: (templateName, templateFile) ->
    alert('loadTemplate ' + templateName)
    
    currentView = @
    templateLoader.loadRemoteTemplate templateName, templateFile, currentView
    return

  onTemplateLoaded: (templateName, templateData) ->
    alert('onTemplateLoaded ' + templateName)
    
    @compiledTemplates[templateName] = _.template(templateData)
    
    ready = true
    $.each @compiledTemplates, (templateName, templateData) ->
      ready = false if templateData is false
      return
    
    @render() if ready
    return

  renderTemplate: (templateName, parameters) ->
    alert('renderTemplate ' + templateName)
    @compiledTemplates[templateName](parameters)

)