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
    $('#content').each ->
      if $(this).data('alertTxt') != ''
        viewClass = appConfigurationView.getConfiguration('', 'showFlashBag')
        new viewClass(
          html: $(this).data('alertTxt')
          domContainer: $('h1.page-title').parent()
        )
    @loadWidgets()

  loadWidgets: ->
    currentView = @
    $.ajax
      type: "GET"
      url: $('#nav-dashboard').data('url')
      success: (response) ->
        for widget in response.widgets
          new window[appConfigurationView.getConfiguration 'dashboard_widgets', widget.type]
            domContainer: $('.widget-container', currentView.$el)
)
