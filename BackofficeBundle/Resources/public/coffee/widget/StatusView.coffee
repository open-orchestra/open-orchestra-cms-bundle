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
    content = $('#content').html()
    displayLoader()
    $.ajax
      url: $(event.currentTarget).data("url")
      data: JSON.stringify({status_id: $(event.currentTarget).data("status")})
      dataType: 'json'
      method: 'POST'
      success: ->
        Backbone.history.loadUrl(Backbone.history.fragment)
      error: (jqXHR, textStatus, errorThrown) ->
        $('#content').html(content)
        viewClass = appConfigurationView.getConfiguration('status', 'showFlashBag')
        new viewClass(
          html: jqXHR.responseJSON[0].message
          domContainer: $('h1.page-title').parent()
        )
    return
)
