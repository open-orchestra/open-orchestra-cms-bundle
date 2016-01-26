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
        generateUrl = currentView.generateUrl
        _.each entities, (row, row_key, list) ->
          _.each row, (value, value_key) ->
            list[row_key][value_key] = if value.length > 20 then value.substr(0, 17) + '...' else value
            return
          return
        data = _.extend({entities: entities}, { generateUrl })
        $('.widget-body', currentView.$el).html currentView.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/listItemView', data)

  generateUrl: (entity) ->
    return

  getUrl: ->
    return

  getListView: ->
    return
)
