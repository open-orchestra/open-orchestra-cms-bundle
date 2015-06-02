VersionView = OrchestraView.extend(
  tagName: "option"

  events:
    'click': 'changeVersion'

  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetVersion"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetVersion',
      element: @options.element
      version: @options.currentVersion.version
    )
    @options.domContainer.prepend @$el
    return

  changeVersion: (event) ->
    event.preventDefault()
    displayLoader()
    redirectUrl = appRouter.generateUrl(@options.currentVersion.path, appRouter.addParametersToRoute(
      version: event.currentTarget.value
      language: @options.currentVersion.language
    ))
    Backbone.history.navigate(redirectUrl, {trigger: true})
)
