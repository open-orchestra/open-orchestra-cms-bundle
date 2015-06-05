TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'
    'keyup input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'entityType'
      'translatedHeader'
      'displayedElements'
      'visibleElements'
      'domContainer'
      'order'
      'title'
      'url'
    ])
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewButtonAdd'
    ]
    return

  render: ->
    parent = $('#nav-'+@options.entityType).parent('[data-type]')
    if (parent.length)
      @options.entityType = parent[0].getAttribute('data-type')

    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView',
      displayedElements: @options.translatedHeader
    )
    @options.domContainer.html @$el

    $('.js-widget-title', @options.domContainer).text @options.title

    columns = []
    columnDefs = []
    for index, element of @options.displayedElements
      columns.push({'data' : element, 'defaultContent': ''});
      columnDefs.push({'name' : element, 'targets': parseInt(index)});
    columns.push({'data' : 'links'})
    viewContext = @

    table = $('#tableviewCollectionTable').dataTable(
      searching: true
      ordering: true
      processing: true
      serverSide: true
      ajax :
        url : @options.url
        dataSrc: (json) ->
          collectionName = json.collection_name
          return json[collectionName]
      initComplete: (settings, json) ->
        viewContext.renderAddButton(viewContext, json.links)
      columns: columns
      columnDefs: columnDefs.concat [
        targets: -1
        data: 'links'
        createdCell : (td, cellData, rowData, row, col) ->
          viewContext.renderColumnActions(viewContext, td, cellData, rowData, row, col)
      ]
      order: [@options.order]
      lengthChange: false
    )

    api = table.api()
    headers = api.columns().header().toArray()
    for i of headers
      if @options.visibleElements.length > 0 && $(api.column(i).header()).text() != '' && @options.visibleElements.indexOf(i.toString()) == -1
       api.column(i).visible(false)
    colvis = new ($.fn.dataTable.ColVis)(table, exclude: [ @options.displayedElements.length ])
    $('.jarviswidget-ctrls').prepend colvis.button()

    return

  renderColumnActions : (viewContext, td, cellData, rowData, row, col) ->
    elementModel = new TableviewModel
    elementModel.set rowData

    new TableviewAction(viewContext.addOption(
      element: elementModel
      domContainer : $(td)
    ))

  renderAddButton: (viewContext, links) ->
    button =  viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewButtonAdd',
      links: links
    )
    viewContext.$el.find('table').after button

  searchColumn : (event) ->
    value = $(event.target).val()
    columnIndex = $(event.target).parent().get(0).cellIndex
    api = @.$el.find('table').dataTable().api()
    api.column(columnIndex).search(value).draw()

  clickAdd: (event) ->
    event.preventDefault()
    displayLoader('div[role="container"]')
    Backbone.history.navigate(@addUrl)
    viewContext = @
    url = $(event.target).data('url')
    $.ajax
      url: url
      method: 'GET'
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'add')
        new viewClass(viewContext.addOption(
          html: response
          extendView: [ 'generateId' ]
          domContainer: $('#content')
        ))
)
