OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    
    if @multiLanguage
      @events['click a.change-language'] = 'changeLanguage'
      templates.push "language"
    
    if @multiStatus
      @events['change select#selectbox'] = 'changeVersion'
      templates.push "widgetStatus"
    
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
      if @multiLanguage
        @addLanguagesToView()
      if @multiStatus
        @renderWidgetStatus()
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
    redirectRoute = appRouter.generateUrl(@multiLanguage.path,
      $.extend(@multiLanguage.path_option, {language: $(event.currentTarget).data('language')})
    )
    Backbone.history.navigate(redirectRoute , {trigger: true})

  renderWidgetStatus: ->
    viewContext = this
    $.ajax
      type: "GET"
      data:
        language: @multiStatus.language
        version: @multiStatus.version
      url: @multiStatus.status_list
      success: (response) ->
        widgetStatus = viewContext.renderTemplate('widgetStatus',
          current_status: viewContext.multiStatus.status
          statuses: response.statuses
          status_change_link: viewContext.multiStatus.status_change_link
        )
        addCustomJarvisWidget(widgetStatus)
        return

  changeVersion: (event) ->
    redirectRoute = appRouter.generateUrl(@multiStatus.path,
      $.extend(@multiStatus.path_option, {language: $(event.currentTarget).data('language'), version: event.currentTarget.value})
    )
    Backbone.history.navigate(redirectRoute , {trigger: true})
    return
)
