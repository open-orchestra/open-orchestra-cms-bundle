DashboardView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboardView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboardView')
    @options.domContainer.html @$el
)
