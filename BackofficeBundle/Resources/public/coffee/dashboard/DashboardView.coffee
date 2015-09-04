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
    @loadWidget()

  loadWidget: ->
    appConfigurationView.setConfiguration('last-node', 'addDashboardWidget', DashboardWidgetListNodeView)
    appConfigurationView.setConfiguration('node-draft', 'addDashboardWidget', DashboardWidgetListNodeView)
    appConfigurationView.setConfiguration('last-content', 'addDashboardWidget', DashboardWidgetListContentView)
    appConfigurationView.setConfiguration('content-draft', 'addDashboardWidget', DashboardWidgetListContentView)

    dashboardLink = $('a[href="#dashboard"]')
    widgets = dashboardLink.data('widget-name').split(",")

    for widget in widgets
      widgetName = widget.trim()
      optionsName = dashboardLink.data('widget-'+widgetName+'-options');
      options = {
        domContainer : $('.widget-container',@$el)
        widgetName : widgetName
      }
      for option in optionsName.split(",")
        options[option.trim()] = dashboardLink.data('widget-'+widgetName+'-'+option.trim())
      viewClass = appConfigurationView.getConfiguration(widgetName, 'addDashboardWidget')
      new viewClass(options)
)
