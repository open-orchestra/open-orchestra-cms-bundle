TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'
    'draw.dt table': 'changePage'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'entityType'
      'translatedHeader'
      'inputHeader'
      'displayedElements'
      'visibleElements'
      'displayGlobalSearch'
      'domContainer'
      'order'
      'title'
      'url'
      'page'
    ])
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewCollectionView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewActions'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewButtonAdd'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewCollectionView',
      {displayedElements: @options.translatedHeader}
    )

    @options.domContainer.html @$el

    $('.js-widget-title', @options.domContainer).text @options.title

    columns = []
    columnDefs = []
    for index, element of @options.displayedElements
      columns.push({'data' : element, 'defaultContent': ''});
      columnDefs.push({
          'title': @options.translatedHeader[index],
          'name' : element,
          'visible': @checkDefaultVisible(element),
          'activateColvis': true,
          'targets': parseInt(index)
          'searchField': if @options.inputHeader[index]? then @options.inputHeader[index] else undefined
      });
    columns.push({'data' : 'links'})
    columnDefs.push(
        targets: -1
        data: 'links'
        orderable: false
        createdCell : (td, cellData, rowData, row, col) ->
          viewContext.renderColumnActions(viewContext, td, cellData, rowData, row, col)
    )
    viewContext = @

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
        initComplete: (settings, json) ->
          viewContext.renderAddButton(viewContext, json.links, this)
    );
    table = datatable.render().el
    $('.tableviewCollectionTable', @options.domContainer).html table
    return

  checkDefaultVisible : (name) ->
    return @options.visibleElements.indexOf(name) >= 0

  renderColumnActions : (viewContext, td, cellData, rowData, row, col) ->

    elementModel = new TableviewModel
    elementModel.set rowData

    tableviewRestoreActionClass = appConfigurationView.getConfiguration('trashcan','addRestoreButtonAction')
    appConfigurationView.setConfiguration('trashcan', 'addButtonAction', tableviewRestoreActionClass)
    tableActionViewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'addButtonAction')

    new tableActionViewClass(viewContext.addOption(
      element: elementModel
      tableId: @options.entityType
      domContainer : $(td)
    ))

  renderAddButton: (viewContext, links, table) ->
    button =  viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewButtonAdd',
      links: links
    )
    $(table).after button

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
        viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'addEntity')
        new viewClass(viewContext.addOption(
          html: response
          extendView: [ 'generateId' ]
          domContainer: $('#content')
        ))

  changePage : (event) ->
    api = $(event.target).DataTable()
    page = api.page.info().page + 1
    url = appRouter.generateUrl('listEntities', entityType: @options.entityType, page: page)
    Backbone.history.navigate(url)

)

((router) ->
  router.addRoutePattern 'loadTranslationDatatable', $('#contextual-informations').data('datatableTranslationUrlPattern')
) window.appRouter
