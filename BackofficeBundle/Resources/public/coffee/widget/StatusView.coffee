StatusView = OrchestraView.extend(
  events:
    'click .change-status': 'changeStatus'

  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetStatus"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetStatus',
      currentStatus: @options.currentStatus.status
      statuses: @options.statuses
      statusChangeLink: @options.currentStatus.self_status_change
    )
    addCustomJarvisWidget(@$el)
    return

  changeStatus: (event) ->
    event.preventDefault()
    displayLoader()
    url = $(event.currentTarget).data("url")
    statusId = $(event.currentTarget).data("status")
    data =
      status_id: statusId
    data = JSON.stringify(data)
    $.post(url, data).always (response) ->
      Backbone.history.loadUrl(Backbone.history.fragment)
      return
    return
)
