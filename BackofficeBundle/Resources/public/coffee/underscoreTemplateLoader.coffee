# Orchestra adaptation of the jQuery templateLoader plugin found at
# https://github.com/Gazler/Underscore-Template-Loader
do ->
  templateLoader =
    templates: {}
    loadRemoteTemplate: (templateName, language, view) ->
      if !@templates[language]
        @addLanguage language
      if !@templates[language][templateName]
        self = this
        filename = appRouter.generateUrl('loadUnderscoreTemplate')
        jQuery.ajax {
          method: "GET"
          url: filename
          data: { 'language': language,'templateId': templateName }
          success: (tpl, textStatus, xhr) ->
            if 200 == xhr.status
              templateLoader.addTemplate templateName, language, tpl
              templateLoader.saveLocalTemplates()
            view.onTemplateLoaded templateName, tpl
            return
        }
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
    localStorageAvailable: ->
      try
        return 'localStorage' of window and window['localStorage'] != null
      catch e
        return false
      return
    saveLocalTemplates: ->
      if @localStorageAvailable
        localStorage.setItem 'templates', JSON.stringify(@templates)
        localStorage.setItem 'templateVersion', @templateVersion
      return
    loadLocalTemplates: (options) ->
      @templateVersion = options.templateVersion
      if @localStorageAvailable
        templateVersion = localStorage.getItem('templateVersion')
        if templateVersion and templateVersion == @templateVersion
          templates = localStorage.getItem('templates')
          if templates
            templates = JSON.parse(templates)
            for language of templates
              if !@templates[language]
                @addLanguage language
              for templateName of templates[language]
                if !@templates[language][templateName]
                  @addTemplate templateName, language, templates[language][templateName]
        else
          localStorage.removeItem 'templates'
          localStorage.removeItem 'templateVersion'
      return
  templateLoader.loadLocalTemplates templateVersion: $('#assets-version').html()
  window.templateLoader = templateLoader
  return

((router) ->
  router.addRoutePattern 'loadUnderscoreTemplate', $('#contextual-informations').data('templateUrlPattern')
) window.appRouter
