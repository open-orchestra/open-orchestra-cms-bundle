OrchestraView = Backbone.View.extend(

  loadTemplates: (templates) ->
    @compiledTemplates = {}
    currentView = @
    Backbone.Wreqr.radio.commands.execute 'widget', 'init', @
    templates.push "smartConfirmButton"
    templates.push "smartConfirmTitle"
    templates.push "widgetPreviewLink" if templates.indexOf("widgetPageConfigurationButton") == -1
    templates.push "widgetPageConfigurationButton" if templates.indexOf("widgetPageConfigurationButton") == -1
    templates.push "widgetFolderConfigurationButton" if templates.indexOf("widgetFolderConfigurationButton") == -1

    $.each templates, (index, templateName) ->
      currentView.compiledTemplates[templateName] = false
      return

    $.each templates, (index, templateName) ->
      currentView.loadTemplate(templateName)
      return
    return

  loadTemplate: (templateName) ->
    templateLoader.loadRemoteTemplate templateName, getCurrentLocale(), @
    return

  onTemplateLoaded: (templateName, templateData) ->
    @compiledTemplates[templateName] = _.template(templateData)
    
    ready = true
    $.each @compiledTemplates, (templateName, templateData) ->
      ready = false if templateData is false
      return
    if ready
      @render()
      Backbone.Wreqr.radio.commands.execute 'widget', 'ready', @
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)
)
