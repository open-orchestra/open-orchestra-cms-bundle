TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'
    'draw.dt table': 'changePage'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'entityType'
      'datatableParameterName'
      'displayGlobalSearch'
      'domContainer'
      'title'
      'url'
      'page'
    ])
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewCollectionView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewActions'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewButtonAdd'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewCollectionView')
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).text @options.title
    if !dataTableConfigurator.dataTableParameters
      @listenToOnce(dataTableConfigurator, 'dataTableParameters_loaded', @createDatatable)
    else
      @createDatatable()

  createDatatable: ->
    columns = []
    columnDefs = []
    columnsParamaters = dataTableConfigurator.getDataTableParameters(@options.datatableParameterName)
    for index, element of columnsParamaters.column_parameter
      columns.push({'data' : element.name, 'defaultContent': ''});
      defs = $.extend({}
        element,
        targets: columnDefs.length
      )
      columnDefs.push(defs);
    initComplete = ((viewContext) ->
      (settings, json) ->
        viewContext.renderAddButton.bind(viewContext) json.links, this
        return
    )(@)
    links = @addLinks()
    columns.push links.columns if typeof links != 'undefined' and typeof links.columns != 'undefined'
    columnDefs.push links.columnDefs if typeof links != 'undefined' and typeof links.columnDefs != 'undefined'
    datatableViewClass = appConfigurationView.getConfiguration(@options.entityType,'addDataTable')
    datatable = new datatableViewClass(
        url: @options.url
        page: if @options.page? then parseInt(@options.page) - 1 else 0
        columns: columns
        columnDefs: columnDefs
        globalSearch: @options.displayGlobalSearch
        tableId: @options.entityType
        tableClassName: 'table table-striped table-bordered table-hover smart-form'
        language:
          url: appRouter.generateUrl('loadTranslationDatatable')
        initComplete: initComplete
    );
    table = datatable.$el
    $('.tableviewCollectionTable', @options.domContainer).html table
    return

  renderColumnActions : (td, cellData, rowData, row, col) ->

    elementModel = new TableviewModel
    elementModel.set rowData
    tableActionViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addButtonAction')

    new tableActionViewClass(@addOption(
      element: elementModel
      tableId: @options.entityType
      domContainer : $(td)
    ))

  renderAddButton: (links, table) ->
    button =  @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewButtonAdd',
      links: links
    )
    $(table).after button
    OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView @, '.ribbon-form-button'

  clickAdd: (event) ->
    event.preventDefault()
    displayLoader('div[role="container"]')
    Backbone.history.navigate(@addUrl)
    url = $(event.target).data('url')
    $.ajax
      url: url
      method: 'GET'
      context: @
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration(@options.entityType, 'addEntity')
        new viewClass(@addOption(
          html: response
          extendView: [ 'generateId' ]
          domContainer: $('#content')
        ))

  changePage : (event) ->
    api = $(event.target).DataTable()
    page = api.page.info().page + 1
    url = appRouter.generateUrl('listEntities', entityType: @options.entityType, page: page)
    Backbone.history.navigate(url)

  addLinks : () ->
    createdCell = ((viewContext) ->
      (td, cellData, rowData, row, col) ->
        viewContext.renderColumnActions.bind(viewContext) td, cellData, rowData, row, col
        return
    )(@)
    {
      'columns': 'data': 'links'
      'columnDefs':
        targets: -1
        data: 'links'
        orderable: false
        createdCell: createdCell
    }
)

((router) ->
  router.addRoutePattern 'loadTranslationDatatable', $('#contextual-informations').data('datatableTranslationUrlPattern')
) window.appRouter
