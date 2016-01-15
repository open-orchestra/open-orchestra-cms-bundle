VersionView = OrchestraView.extend(
  tagName: "option"
  extendView : [ 'concurrency' ]

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
)
