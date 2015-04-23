languageChannel = Backbone.Wreqr.radio.channel('language')

languageChannel.commands.setHandler 'ready', (view) ->
  $.ajax
    type: "GET"
    url: view.options.multiLanguage.language_list
    success: (response) ->
      site = new Site
      site.set response
      for language of site.get('languages')
        new LanguageView(
          language: site.get('languages')[language]
          domContainer: view.$el.find('#entity-languages')
          currentLanguage: view.options.multiLanguage
        )
      return
