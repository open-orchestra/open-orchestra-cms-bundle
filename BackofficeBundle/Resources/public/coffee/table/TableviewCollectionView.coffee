search = (api, rank) ->
  return ->
    api.column(rank).search($(this).val()).draw()
    return

TableviewCollectionView = OrchestraView.extend(
  events:
    'click #none': 'clickAdd'

  initialize: (options) ->
    @options = options
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    key = 'click a.ajax-add-' + @cid
    @events[key] = 'clickAdd'
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewCollectionView',
      'tableviewView',
      'tableviewActions'
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
        $(viewContext.el).html viewContext.renderTemplate('tableviewCollectionView',
          displayedElements: response.displayed_elements
          links: viewContext.options.elements.get('links')
          cid: viewContext.cid
        )
    $('.js-widget-title', @$el).text @options.title
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
    options = @options
    elementModel = new TableviewModel
    elementModel.set elementData
    view = new TableviewView(
      element: elementModel
      displayedElements: options.displayedElements
      title: options.title
      entityType: options.entityType
      el : this.$el.find('tbody')
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
