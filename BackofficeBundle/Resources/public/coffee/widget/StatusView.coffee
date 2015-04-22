StatusView = OrchestraView.extend(

  initialize: (options) ->
    @events = {}
    @events['click .change-status'] = 'changeStatus'
    @options = options
    @loadTemplates [
      "widgetStatus"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetStatus',
      currentStatus: @options.currentStatus.status
      statuses: @options.statuses
      statusChangeLink: @options.currentStatus.self_status_change
    )
    addCustomJarvisWidget(@$el)
    return

  changeStatus: (event) ->
    event.preventDefault()
    url = $(event.currentTarget).data("url")
    statusId = $(event.currentTarget).data("status")
    displayLoader()
    data =
      status_id: statusId
    data = JSON.stringify(data)
    $.post(url, data).always (response) ->
      Backbone.history.loadUrl(Backbone.history.fragment)
      return
    return
)
