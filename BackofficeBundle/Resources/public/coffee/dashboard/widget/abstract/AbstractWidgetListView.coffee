AbstractWidgetListView = OrchestraView.extend(

  initialize: (options) ->
    @options = options
    @loadTemplates [
      @getListView()
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/listItemView"
    ]
    return

  render: ->
    @setElement @renderTemplate(@getListView())
    @options.domContainer.append @$el
    displayLoader('.widget-body', @$el)
    @loadElement()
    return

  loadElement : ->
    currentView = @
    $.ajax
      url: @getUrl()
      success: (response) ->
        collectionName = response.collection_name
        entities = response[collectionName]
        truncateEntities = $.extend(true, {}, entities)
        generateUrl = currentView.generateUrl
        _.each truncateEntities, (row, row_key) ->
          _.each row, (value, value_key) ->
            truncateEntities[row_key][value_key] = if typeof value == 'string' and value.length > 20 then value.substr(0, 17) + '...' else value
            return
          return
        data = _.extend({entities: entities, truncateEntities: truncateEntities}, { generateUrl })
        $('.widget-body', currentView.$el).html currentView.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/listItemView', data)

  generateUrl: (entity) ->
    return

  getUrl: ->
    return

  getListView: ->
    return
)
