OrchestraView = Backbone.View.extend(

  compiledTemplates: {}

  loadTemplates: (templates) ->
    currentView = @
    if @multiLanguage
      @events['click a.change-language'] = 'changeLanguage'
      templates.push "language"
    if @multiStatus
      @events['click a.change-status'] = 'changeStatus'
      templates.push "widgetStatus"
    if @multiVersion
      @events['change select#selectbox'] = 'changeVersion'
      templates.push "choice"
    
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
      @renderWidgetStatus() if @multiStatus
      @addVersionToView() if @multiVersion
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
    redirectUrl = appRouter.generateUrl(@multiLanguage.path, appRouter.addParametersToRoute(
      language: $(event.currentTarget).data('language')
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})

  renderWidgetStatus: ->
    viewContext = @
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
          status_change_link: viewContext.multiStatus.self_status_change
        )
        addCustomJarvisWidget(widgetStatus)
        return

  changeStatus: (event) ->
    url = $(event.currentTarget).data("url")
    statusId = $(event.currentTarget).data("status")
    displayLoader()
    data =
      status_id: newStatusId
    data = JSON.stringify(data)
    $.post(url, data).always (response) ->
      Backbone.history.loadUrl(Backbone.history.fragment)
      return
    return

  addVersionToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @multiVersion.self_version
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
      version: @multiVersion.version
      el: this.$el.find('optgroup#versions')
    )

  changeVersion: (event) ->
    redirectUrl = appRouter.generateUrl(@multiVersion.path, appRouter.addParametersToRoute(
      version: event.currentTarget.value
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})
