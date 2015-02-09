duplicateChannel = Backbone.Wreqr.radio.channel('duplicate')

duplicateChannel.commands.setHandler 'init', (view) ->
  view.events['click .btn-new-version-' + view.cid] = 'duplicateElement'
  view.duplicateElement = (event) ->
    redirectUrl = appRouter.generateUrl(view.options.duplicate.path, appRouter.addParametersToRoute(
      language: view.options.duplicate.language
    ))
    $.ajax
      url: view.options.duplicate.self_duplicate
      method: 'POST'
      success: ->
        if (redirectUrl != Backbone.history.fragment)
          Backbone.history.navigate(redirectUrl, {trigger: true})
        else
          Backbone.history.loadUrl()
    return
