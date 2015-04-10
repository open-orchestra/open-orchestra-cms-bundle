domBinded = {}

OrchestraView = Backbone.View.extend(

  constructor: (attributes, options) ->
    Backbone.View.apply @, arguments
    if domBinded[@$el]
      domBinded[@$el].undelegateEvents()
    domBinded[@$el] = @
    return

  loadTemplates: (templates) ->
    @compiledTemplates = {}
    currentView = @
    Backbone.Wreqr.radio.commands.execute 'widget', 'init', @

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
      Backbone.Wreqr.radio.commands.execute 'widget', 'ready', @
      return @render()
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)
)
