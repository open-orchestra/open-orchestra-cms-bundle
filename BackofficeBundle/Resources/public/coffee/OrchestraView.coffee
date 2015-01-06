OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    if @options
      if @options.multiLanguage
        @events['click a.change-language'] = 'changeLanguage'
        templates.push "language"
      if @options.multiStatus
        @events['click a.change-status'] = 'changeStatus'
        templates.push "widgetStatus"
      if @options.multiVersion
        @events['change select#selectbox'] = 'changeVersion'
        templates.push "elementChoice"
        templates.push "elementTitle"
      if @options.duplicate
        @events['click a#btn-new-version'] = 'duplicateElement'
    templates.push "smartConfirmButton"
    templates.push "smartConfirmTitle"
    templates.push "widgetPreviewLink" if templates.indexOf("widgetPageConfigurationButton") == -1
    templates.push "widgetPageConfigurationButton" if templates.indexOf("widgetPageConfigurationButton") == -1

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
        @addLanguagesToView() if @options.multiLanguage
        @renderWidgetStatus() if @options.multiStatus
        @addVersionToView() if @options.multiVersion
    return

  renderTemplate: (templateName, parameters) ->
    @compiledTemplates[templateName](parameters)

  addLanguagesToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @options.multiLanguage.language_list
      success: (response) ->
        site = new Site
        site.set response
        for language of site.get('languages')
          viewContext.addLanguageToPanel(site.get('languages')[language])
        return

  addLanguageToPanel: (language) ->
    view = new LanguageView(
      language: language
      currentLanguage: @options.multiLanguage.language
      el: this.$el.find('#entity-languages')
    )

  changeLanguage: (event) ->
    redirectUrl = appRouter.generateUrl(@options.multiLanguage.path, appRouter.addParametersToRoute(
      language: $(event.currentTarget).data('language')
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})

  renderWidgetStatus: ->
    viewContext = @
    $.ajax
      type: "GET"
      data:
        language: @options.multiStatus.language
        version: @options.multiStatus.version
      url: @options.multiStatus.status_list
      success: (response) ->
        widgetStatus = viewContext.renderTemplate('widgetStatus',
          current_status: viewContext.options.multiStatus.status
          statuses: response.statuses
          status_change_link: viewContext.options.multiStatus.self_status_change
        )
        addCustomJarvisWidget(widgetStatus)
        return

  changeStatus: (event) ->
    url = $(event.currentTarget).data("url")
    statusId = $(event.currentTarget).data("status")
    displayLoader()
    data =
      status_id: statusId
    data = JSON.stringify(data)
    $.post(url, data).always (response) ->
      Backbone.history.loadUrl(Backbone.history.fragment)
      return
    return

  addVersionToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @options.multiVersion.self_version
      success: (response) ->
        collection = new TableviewElement
        collection.set response
        collectionName = collection.get('collection_name')
        for version of collection.get(collectionName)
          viewContext.addChoiceToSelectBox(collection.get(collectionName)[version])
        return

  addChoiceToSelectBox: (version) ->
    versionElement = new TableviewModel
    versionElement.set version
    view = new VersionView(
      element: versionElement
      version: @options.multiVersion.version
      el: this.$el.find('#version-selectbox')
    )

  changeVersion: (event) ->
    redirectUrl = appRouter.generateUrl(@options.multiVersion.path, appRouter.addParametersToRoute(
      version: event.currentTarget.value
      language: @options.multiVersion.language
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})

  duplicateElement: ->
    redirectUrl = appRouter.generateUrl(@options.duplicate.path, appRouter.addParametersToRoute(
      language: @options.duplicate.language
    ))
    $.ajax
      url: @options.duplicate.self_duplicate
      method: 'POST'
      success: ->
        Backbone.history.loadUrl(redirectUrl)
    return

)
