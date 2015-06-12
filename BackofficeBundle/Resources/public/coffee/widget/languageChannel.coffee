widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.multiLanguage
    $.ajax
      type: "GET"
      url: view.options.multiLanguage.language_list
      success: (response) ->
        site = new Site
        site.set response
        for language of site.get('languages')
          viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showLanguage')
          new viewClass(
              language: site.get('languages')[language]
              domContainer: view.$el.find('#entity-languages')
              currentLanguage: view.options.multiLanguage
            )
        return
