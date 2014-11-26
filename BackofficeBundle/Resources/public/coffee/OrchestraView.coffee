OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    $.each templates, (index, templateName) ->
      currentView.compiledTemplates[templateName] = false
      return
    
#    console.log('>>>> Starting templates loading >>>>')
    $.each templates, (index, templateName) ->
      currentView.loadTemplate(templateName)
      return
    return

  loadTemplate: (templateName) ->
#    console.log('Loading template ' + templateName)
    
    templateLoader.loadRemoteTemplate templateName, getCurrentLocale(), @
    return

  onTemplateLoaded: (templateName, templateData) ->
    @compiledTemplates[templateName] = _.template(templateData)
#    console.log(templateName + ' compiled')
    
    ready = true
    $.each @compiledTemplates, (templateName, templateData) ->
      ready = false if templateData is false
      return
    
#    @render() if ready
    if ready
#      console.log('<<<< Templates loading ended <<<<')
      @render()
    return

  renderTemplate: (templateName, parameters) ->
#    console.log('=== Rendering ' + templateName + ' in ===')
#    console.log(@el)
#    console.log('With params')
#    console.log(parameters)
    
    @compiledTemplates[templateName](parameters)

)
