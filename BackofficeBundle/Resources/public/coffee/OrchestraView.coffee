OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    if @options
      if @options.multiLanguage
        widgetChannel.commands.execute 'initMultiLanguage', @
        templates = templates.concat widgetChannel.reqres.request 'initMultiLanguage'
      if @options.multiStatus
        widgetChannel.commands.execute 'initMultiStatus', @
        templates = templates.concat widgetChannel.reqres.request 'initMultiStatus'
      if @options.multiVersion
        widgetChannel.commands.execute 'initMultiVersion', @
        templates = templates.concat widgetChannel.reqres.request 'initMultiVersion'
      if @options.duplicate
        widgetChannel.commands.execute 'initDuplicate', @
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
      if @options
        widgetChannel.commands.execute 'addMultiLanguage', @  if @options.multiLanguage
        widgetChannel.commands.execute 'addMultiStatus', @  if @options.multiStatus
        widgetChannel.commands.execute 'addMultiVersion', @  if @options.multiVersion
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)

)
