OrchestraView = Backbone.View.extend(
  extendView: []

  constructor: (attributes, options) ->
    if attributes && attributes.extendView
      $.extend(@extendView, attributes.extendView)
    for i of @extendView
      $.extend(true, @, extendView[@extendView[i]])
    Backbone.View.apply @, arguments

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
      Backbone.Wreqr.radio.commands.execute 'widget', 'ready', @
      @render()
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)

  reduceOption: (options, keys) ->
    cleanOptions = {}
    $.each options, (key, value) ->
      cleanOptions[key] = value if $.inArray(key, keys) != -1
      return
    return cleanOptions

  addOption: (options) ->
    return $.extend(true, {}, @options, options)
)
