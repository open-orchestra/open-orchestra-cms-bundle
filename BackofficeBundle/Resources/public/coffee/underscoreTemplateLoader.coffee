# Orchestra adaptation of the jQuery templateLoader plugin found at
# https://github.com/Gazler/Underscore-Template-Loader

do ->
  templateLoader =

    templates: {}
    templateVersion: undefined
    environment: undefined

    init: (templateVersion, environment) ->
      @templateVersion = templateVersion
      @environment = environment

      if @isAvailableLocalStorage
        storageTemplateVersion = localStorage.getItem 'templateVersion'

        if storageTemplateVersion and storageTemplateVersion == @templateVersion
          templates = localStorage.getItem 'templates-' + @environment

          if templates
            templates = JSON.parse templates
            for language of templates
              if !@templates[language]
                @addLanguage language
              for templateName of templates[language]
                if !@templates[language][templateName]
                  @addTemplate templateName, language, templates[language][templateName]
        else
          localStorage.removeItem 'templates-' + @environment
          localStorage.removeItem 'templateVersion'
      return

    loadRemoteTemplate: (templateName, language, view) ->
      if !@templates[language]
        @addLanguage language
      if !@templates[language][templateName]
        templateLoader = this
        filename = appRouter.generateUrl('loadUnderscoreTemplate')
        jQuery.get filename, {
          'language': language
          'templateId': templateName
        }, (tpl) ->
          templateLoader.addTemplate templateName, language, tpl
          templateLoader.storeTemplates()
          view.onTemplateLoaded templateName, tpl
          return
      else
        view.onTemplateLoaded templateName, @templates[language][templateName]
      return

    addTemplate: (templateName, language, tpl) ->
      if !@templates[language]
        @addLanguage language
      @templates[language][templateName] = tpl
      return

    addLanguage: (language) ->
      @templates[language] = {}
      return

    isAvailableLocalStorage: ->
      try
        return 'localStorage' of window and window['localStorage'] != null
      catch e
        return false
      return

    storeTemplates: ->
      if @isAvailableLocalStorage
        localStorage.setItem 'templates-' + @environment, JSON.stringify(@templates)
        localStorage.setItem 'templateVersion', @templateVersion
      return

  templateLoader.init $('#assets-version').html(), $('#oo-environment').html()
  window.templateLoader = templateLoader
  return

((router) ->
  router.addRoutePattern 'loadUnderscoreTemplate', $('#contextual-informations').data('templateUrlPattern')
) window.appRouter
