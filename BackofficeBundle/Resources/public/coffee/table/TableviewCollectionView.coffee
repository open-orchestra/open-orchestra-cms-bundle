search = (api, rank) ->
  return ->
    api.column(rank).search($(this).val()).draw()
    return

TableviewCollectionView = OrchestraView.extend(
  initialize: (options) ->
    @events = []
    @events['click a.ajax-add'] = 'clickAdd'
    @options = options
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewCollectionView',
    ]
    return

  render: ->
    $(@el).html @renderTemplate('tableviewCollectionView',
      displayedElements: @options.displayedElements
      links: @options.elements.get('links')
    )
    $('.js-widget-title', @$el).text @options.title
    name = @options.elements.get('collection_name')
    for element of @options.elements.get(name)
      @addElementToView (@options.elements.get(name)[element])
      
    $('#tableviewCollectionTable').dataTable(
      searching: true
      ordering: true
      order: [@options.order]
      lengthChange: false
      initComplete: ->
        api = @.api()
        tr = $('<tr>')
        headers = api.columns().header().toArray()
        for i of headers
          td = $('<td>')
          text = $(api.column(i).header()).text()
          if text != ''
            input = $('<input>')
            .attr('type', 'text')
            .attr('placeholder', 'Search ' + text)
            .on('keyup change', search(api, i))
            td.append input
          tr.append td
        $(api.table().header()).append tr
        return
    )
    
    return

  addElementToView: (elementData) ->
    options = @options
    elementModel = new TableviewModel
    elementModel.set elementData
    new TableviewView(
      element: elementModel
      displayedElements: options.displayedElements
      title: options.title
      entityType: options.entityType
      target : @$el.find('tbody')
    )
    return

  clickAdd: (event) ->
    event.preventDefault()
    displayLoader('div[role="container"]')
    Backbone.history.navigate(@addUrl)
    options = @options
    $.ajax
      url: options.elements.get('links')._self_add
      method: 'GET'
      success: (response) ->
        view = new FullPageFormView(
          html: response
          title: options.title
          entityType: options.entityType
          element: options.element
          triggers: [
            {
              event: "focusout input.generate-id-source"
              name: "generateId"
              fct: generateId
            }
            {
              event: "blur input.generate-id-dest"
              name: "stopGenerateId"
              fct: stopGenerateId
            }
          ]
        )
)
