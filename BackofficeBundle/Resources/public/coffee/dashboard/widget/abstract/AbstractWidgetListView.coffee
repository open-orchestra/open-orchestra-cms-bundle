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
        data = _.extend({entities: entities}, { generateUrl })
        _.each data, (value, key, list) ->
          list[key] = _.truncate(value, 10)
          return
        $('.widget-body', currentView.$el).html currentView.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/listItemView', data)

  generateUrl: (entity) ->
    return

  getUrl: ->
    return

  getListView: ->
    return
)
