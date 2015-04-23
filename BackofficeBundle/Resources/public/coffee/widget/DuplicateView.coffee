DuplicateView = OrchestraView.extend(

  initialize: (options) ->
    @events = {}
    @events['click'] = 'duplicateElement'
    @options = options
    @loadTemplates [
      "widgetDuplicate"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetDuplicate',
      text: @options.domContainer.data('text')
    )
    @options.domContainer.append @$el
    return

  duplicateElement: (event) ->
    redirectUrl = appRouter.generateUrl(@options.currentDuplicate.path, appRouter.addParametersToRoute(
      language: @options.currentDuplicate.language
    ))
    $.ajax
      url: @options.currentDuplicate.self_duplicate
      method: 'POST'
      success: ->
        if (redirectUrl != Backbone.history.fragment)
          Backbone.history.navigate(redirectUrl, {trigger: true})
        else
          Backbone.history.loadUrl()
    return
)
