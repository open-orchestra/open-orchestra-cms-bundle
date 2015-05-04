DashboardView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboardView"
    ]
    return

  render: ->
    $(@el).html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboardView')
    return
)
