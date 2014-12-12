OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    if @multiLanguage
      @events['click a.change-language'] = 'changeLanguage'
      templates.push "language"
    
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
      @addLanguagesToView() if @multiLanguage
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)

  addLanguagesToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @multiLanguage.language_list
      success: (response) ->
        site = new Site
        site.set response
        for language of site.get('languages')
          viewContext.addLanguageToPanel(site.get('languages')[language])
        return

  addLanguageToPanel: (language) ->
    view = new LanguageView(
      language: language
      currentLanguage: @multiLanguage.language
      el: this.$el.find('#entity-languages')
    )

  changeLanguage: (event) ->
    appRouter.addParametersToRoute(
      ':language': $(event.currentTarget).data('language')
    )
)
