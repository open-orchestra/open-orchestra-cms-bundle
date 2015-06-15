VersionSelectView = OrchestraView.extend(
  tagName: "select"

  events:
    'change': 'changeVersion'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'currentVersion'
      'versions'
      'domContainer'
      'entityType'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetVersionSelect"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetVersion"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetVersionSelect')
    @options.domContainer.replaceWith @$el
    for version of @options.versions
      versionElement = new VersionviewModel
      versionElement.set @options.versions[version]
      viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showVersion')
      new viewClass(
        element: versionElement
        currentVersion: @options.currentVersion
        domContainer: @$el
        entityType: @options.entityType
      )
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
