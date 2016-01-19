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
    @$el.attr('data-widget-index', @options.widget_index)
    addCustomJarvisWidget(@$el, @options.domContainer)
    return

  changeStatus: (event) ->
    event.preventDefault()
    $.ajax
      url: $(event.currentTarget).data("url")
      data: JSON.stringify({status_id: $(event.currentTarget).data("status")})
      method: 'POST'
      success: ->
        Backbone.history.loadUrl(Backbone.history.fragment)
    return
)
