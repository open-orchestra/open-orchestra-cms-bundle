VersionView = OrchestraView.extend(
  tagName: "option"

  initialize: (options) ->
    @events = {}
    @events['click'] = 'changeVersion'
    @options = options
    @loadTemplates [
      "widgetVersion"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetVersion',
      element: @options.element
      version: @options.currentVersion.version
    )
    @options.domContainer.prepend @$el
    return

  changeVersion: (event) ->
    redirectUrl = appRouter.generateUrl(@options.currentVersion.path, appRouter.addParametersToRoute(
      version: event.currentTarget.value
      language: @options.currentVersion.language
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})
)
