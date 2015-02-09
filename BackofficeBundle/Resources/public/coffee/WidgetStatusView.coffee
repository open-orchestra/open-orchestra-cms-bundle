WidgetStatusView = OrchestraView.extend(

  initialize: (options) ->
    @options = options
    @loadTemplates [
      "widgetStatus"
    ]
    return

  render: ->
    html = @renderTemplate('widgetStatus',
      current_status: @options.current_status
      statuses: @options.statuses
      status_change_link: @options.status_change_link
      cid: @options.cid
    )
    addCustomJarvisWidget(html)
    return
)
