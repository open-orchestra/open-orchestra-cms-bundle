DashboardView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @loadTemplates [
      "dashboardView"
    ]
    return

  render: ->
    $(@el).html @renderTemplate('dashboardView')
    return
)
