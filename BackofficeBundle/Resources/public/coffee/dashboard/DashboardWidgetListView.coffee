DashboardWidgetListView = OrchestraView.extend(
  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardWidgetListView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardWidgetListItemView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardWidgetListView',
      title: @options.title
      widget_name: @options.widgetName
    )
    @options.domContainer.append @$el
    displayLoader('.widget-body', @$el)
    @loadElement()
    return

  loadElement : ->
    currentView = @
    $.ajax
      url : @options.url
      success: (response) ->
        collectionName = response.collection_name
        entities = response[collectionName]
        generateUrl = currentView.generateUrl
        data = _.extend({entities: entities}, { generateUrl })
        $('.widget-body', currentView.$el).html currentView.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/dashboardWidgetListItemView', data)

  generateUrl: (entity) ->
    return
)
