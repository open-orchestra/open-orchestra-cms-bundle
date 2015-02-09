languageChannel = Backbone.Wreqr.radio.channel('language')

languageChannel.commands.setHandler 'init', (view) ->
  view.events['click a.change-language-' + view.cid] = 'changeLanguage'
  view.changeLanguage = (event) ->
    redirectUrl = appRouter.generateUrl(view.options.multiLanguage.path, appRouter.addParametersToRoute(
      language: $(event.currentTarget).data('language')
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})

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
          currentLanguage: view.options.multiLanguage.language
          el: view.$el.find('#entity-languages')
          cid: view.cid
        )
      return
