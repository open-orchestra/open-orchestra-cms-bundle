DashboardView = OrchestraView.extend(

  initialize: (options) ->
    @options = @reduceOption(options, [
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardView')
    @options.domContainer.html @$el
    @loadWidgets()

  loadWidgets: ->
    currentView = @
    $.ajax
      type: "GET"
      url: $('a[href="#dashboard"]').data("url")
      success: (response) ->
        for widget in response.widgets
          new window[appConfigurationView.getConfiguration 'dashboard_widgets', widget.type]
            domContainer: $('.widget-container', currentView.$el)
)
