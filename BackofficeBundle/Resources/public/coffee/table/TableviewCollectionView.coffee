search = (api, rank) ->
  return ->
    api.column(rank).search($(this).val()).draw()
    return

TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'

  initialize: (options) ->
    @options = options
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView'
    ]
    return

  render: ->
    viewContext = @
    entityType = @options.entityType
    parent = $('#nav-'+@options.entityType).parent('[data-type]')
    if (parent.length)
      entityType = parent[0].getAttribute('data-type')
    $.ajax
      url: @options.elements.get('links')._translate
      method: 'GET'
      async: false
      data: 
        entityType: entityType
        displayedElements: @options.displayedElements
      success: (response) ->
        viewContext.setElement viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView',
          displayedElements: response.displayed_elements
          links: viewContext.options.elements.get('links')
        )
        viewContext.options.domContainer.html viewContext.$el
    $('.js-widget-title', @options.domContainer).text @options.title
    for element of @options.elements.get(@options.elements.get('collection_name'))
      @addElementToView (@options.elements.get(@options.elements.get('collection_name'))[element])
      
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
          input = $('<input>')
          td = $('<td>')
          text = $(api.column(i).header()).text()
          if text != ''
            input.attr 'type', 'text'
            input.attr 'placeholder', 'Search ' + $(api.column(i).header()).text()
            input.on 'keyup change', search(api, i)
            td.append input
          tr.append td
        $(api.table().header()).append tr
        return
    )
    
    return

  addElementToView: (elementData) ->
    elementModel = new TableviewModel
    elementModel.set elementData
    new TableviewView(@addOption(
      element: elementModel
      domContainer : @$el.find('tbody')
    ))
    return

  clickAdd: (event) ->
    event.preventDefault()
    displayLoader('div[role="container"]')
    Backbone.history.navigate(@addUrl)
    options = @options
    viewContext = @
    $.ajax
      url: options.elements.get('links')._self_add
      method: 'GET'
      success: (response) ->
        view = new FullPageFormView(viewContext.addOption(
          html: response
          extendView: [ 'generateId' ]
        ))
)
