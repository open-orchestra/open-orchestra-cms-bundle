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
#  
#@multiLanguage.language_list
#@multiLanguage.language
#@multiLanguage.url_entity
#@multiLanguage.path
#@multiLanguage.path_option
#  
#    @multiLanguage = 
#      language: @node.get('language')
#      language_list: @node.get('links')._language_list
#      path: 'showNodeWithLanguage'
#      path_option: {nodeId : @node.get('node_id')}
#
#  
#    $(@el).append('<span style="display:none;" data-url="' + @multiLanguage.url_entity + '" id="url-entity" />') if @multiLanguage.url_entity
#    
#    redirectRoute = appRouter.generateUrl(@multiLanguage.path,
#      $.extend(@multiLanguage.path_option, {language: $(event.currentTarget).data('language')})
#    )
#    Backbone.history.navigate(redirectRoute , {trigger: true})
)
