OrchestraView = Backbone.View.extend(
  extendView: []

  constructor: (attributes, options) ->
    if attributes && attributes.extendView
      for i of attributes.extendView
        $.extend(true, @, extendView[attributes.extendView[i]])
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
      @render()
      widgetChannel.trigger 'ready', @
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

  completeOptions: (element, path) ->
    @options = $.extend(@options, multiLanguage:
      language_list : element.get('links')._language_list
      language : element.get('language')
      path: path.multiLanguage
    ) if element.get('links')._language_list

    @options = $.extend(@options, multiStatus:
      language: element.get('language')
      version: element.get('version')
      status_list: element.get('links')._status_list
      status: element.get('status')
      self_status_change: element.get('links')._self_status_change
    ) if element.get('links')._status_list

    @options = $.extend(@options, multiVersion:
      language: element.get('language')
      version: element.get('version')
      self_version: element.get('links')._self_version
      path: path.multiVersion
    ) if element.get('links')._self_version

    @options = $.extend(@options, duplicate:
      language: element.get('language')
      self_duplicate: element.get('links')._self_duplicate
      path: path.duplicate
    ) if element.get('links')._self_duplicate
)
