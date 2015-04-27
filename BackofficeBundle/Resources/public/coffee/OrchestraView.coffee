OrchestraView = Backbone.View.extend(

  constructor: (attributes, options) ->
    Backbone.View.apply @, arguments
    if attributes && attributes.inheritance
      for i of attributes.inheritance
        $.extend(true, @, inheritance[attributes.inheritance[i]])
    @delegateEvents()

  loadTemplates: (templates) ->
    @compiledTemplates = {}
    currentView = @

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

  reduce: (options, keys) ->
    cleanOptions = {}
    $.each options, (key, value) ->
      cleanOptions[key] = value if $.inArray(key, keys) != -1
      return
    return cleanOptions
)
