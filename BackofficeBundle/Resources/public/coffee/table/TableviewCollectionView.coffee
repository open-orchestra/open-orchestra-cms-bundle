TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'
    'keyup input.search-column': 'searchColumn'
    'draw.dt table': 'changePage'
    'processing.dt table': 'processingData'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'entityType'
      'translatedHeader'
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
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewButtonAdd'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView',
      {displayedElements: @options.translatedHeader}
    )
    @options.domContainer.html @$el

    $('.js-widget-title', @options.domContainer).text @options.title

    columns = []
    columnDefs = []
    for index, element of @options.displayedElements
      columns.push({'data' : element, 'defaultContent': ''});
      columnDefs.push({'name' : element, 'visible': @checkDefaultVisible(element), 'targets': parseInt(index)});
    columns.push({'data' : 'links'})
    viewContext = @

    $.fn.dataTable.Api.register('clearPipeline()', ->
      return this.iterator( 'table', (settings) ->
        settings.clearCache = true;
      );
    );
    $.fn.dataTable.pipeline = @dataTablePipeline
    $.extend( $.fn.dataTableExt.oStdClasses, {
      "sWrapper": "dataTables_wrapper form-inline",
      "sFilterInput": "form-control",
      "sLengthSelect": "form-control"
    } );
    displayStart = 0
    pageLength = 10
    if @options.page?
      page = parseInt(@options.page) - 1
      displayStart = pageLength * page

    dom = "<'dt-toolbar'"
    dom += "<'col-xs-12 col-sm-6'f>" if @options.displayGlobalSearch
    numberColum = if @options.displayGlobalSearch then 5 else 11
    dom += "<'col-sm-"+numberColum+" col-xs-6 hidden-xs'C><'col-xs-12 col-sm-1 hidden-xs'l>>"
    dom += "t"
    dom += "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>"

    @options.table = $('#tableviewCollectionTable').dataTable(
      searching: true
      ordering: true
      serverSide: true
      displayStart: displayStart
      pageLength: pageLength
      orderCellsTop: true
      processing: true
      autowidth: false
      pagingType: "input_full"
      dom: dom
      language: {
        url: appRouter.generateUrl('loadTranslationDatatable')
      }
      colVis: exclude: [ viewContext.options.displayedElements.length ]
      ajax : $.fn.dataTable.pipeline(
        url : @options.url
        pages: 5
      )
      fnServerParams: (aoData) ->
        aoData.search = viewContext.transformerDataSearch(aoData)
        aoData.order = viewContext.transformDataOrder(aoData)
        delete aoData.columns
        delete aoData.draw
      initComplete: (settings, json) ->
        $('#tableviewCollectionTable_filter label').prepend('<span class="input-group-addon"><i class="fa fa-search"></i></span>')
        viewContext.renderAddButton(viewContext, json.links, this)
      columns: columns
      columnDefs: columnDefs.concat [
        targets: -1
        data: 'links'
        orderable: false
        createdCell : (td, cellData, rowData, row, col) ->
          viewContext.renderColumnActions(viewContext, td, cellData, rowData, row, col)
      ]
      order: [@options.order]
      bStateSave: true
      fnStateSaveCallback: (oSettings, oData) ->
      	localStorage.setItem 'DataTables_' + location.hash, JSON.stringify(oData)
      	return
      fnStateLoadCallback: (oSettings) ->
      	JSON.parse localStorage.getItem('DataTables_' + location.hash)
    )

    return
  processingData : (e, seggings, processing) ->
    if processing
      $('.dataTables_processing').show()
    else
      $('.dataTables_processing').hide()
    return

  checkDefaultVisible : (name) ->
    return @options.visibleElements.indexOf(name) >= 0

  transformerDataSearch : (data) ->
    search =
      columns : {}
    for column in data.columns
      if column.searchable = true and column.search.value != '' and column.name != ''
        name = column.name
        search.columns[name] = column.search.value
    if data.search.value != ''
      search['global'] = data.search.value
    return search

  transformDataOrder: (data) ->
    for order in data.order
      if data.columns[order.column]? and data.columns[order.column].orderable = true
          name = data.columns[order.column].name if data.columns[order.column]?
          dir = order.dir
          return name: name, dir:dir
    return null

  renderColumnActions : (viewContext, td, cellData, rowData) ->
    elementModel = new TableviewModel
    elementModel.set rowData

    tableviewRestoreActionClass = appConfigurationView.getConfiguration('trashcan','addRestoreButtonAction')
    appConfigurationView.setConfiguration('trashcan', 'addButtonAction', tableviewRestoreActionClass)
    tableActionViewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'addButtonAction')

    new tableActionViewClass(viewContext.addOption(
      element: elementModel
      domContainer : $(td)
    ))

  renderAddButton: (viewContext, links, table) ->
    button =  viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewButtonAdd',
      links: links
    )
    $(table).after button

  searchColumn : (event) ->
    value = $(event.target).val()
    columnIndex = $(event.target).closest("td").get(0).cellIndex
    api = @.$el.find('table').dataTable().api()
    api.column(columnIndex+':visible').search(value).draw()

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
    api = $(event.target).dataTable().api()
    page = api.page.info().page + 1
    url = appRouter.generateUrl('listEntities', entityType: @options.entityType, page: page)
    Backbone.history.navigate(url)

  dataTablePipeline : (opts) ->
    conf = $.extend(
      pages: 5
      method: 'GET'
    , opts );

    cacheLower = -1
    cacheUpper = null
    cacheLastRequest = null
    cacheLastJson = null
    return (request, drawCallback, settings) ->
      ajax = false
      requestStart  = request.start
      drawStart     = request.start
      requestLength = request.length
      requestEnd    = requestStart + requestLength

      if settings.clearCache
          ajax = true
          settings.clearCache = false
      else if cacheLower < 0 or requestStart < cacheLower or requestEnd > cacheUpper or
        JSON.stringify(request.order)   != JSON.stringify(cacheLastRequest.order) or
        JSON.stringify(request.columns) != JSON.stringify(cacheLastRequest.columns) or
        JSON.stringify(request.search)  != JSON.stringify(cacheLastRequest.search)
          ajax = true

      cacheLastRequest = $.extend( true, {}, request)

      if ajax
        if requestStart < cacheLower
          requestStart = requestStart - (requestLength*(conf.pages-1))
          requestStart = 0 if requestStart < 0

        cacheLower = requestStart
        cacheUpper = requestStart + (requestLength * conf.pages)
        request.start = requestStart;
        request.length = requestLength*conf.pages;

        if $.isFunction(conf.data)
          d = conf.data(request)
          $.extend(request, d) if d
        else if $.isPlainObject(conf.data)
          $.extend(request, conf.data)

        settings.jqXHR = $.ajax(
          type:     conf.method
          url:      conf.url
          data:     request
          dataType: "json"
          cache:    false,
          success:  (json) ->
            cacheLastJson = $.extend(true, {}, json)
            data = json[json.collection_name]
            data.splice(0, drawStart-cacheLower) if cacheLower != drawStart
            data.splice(requestLength, data.length)
            settings.sAjaxDataProp = json.collection_name
            drawCallback(json)
        )
      else
        json = $.extend(true, {}, cacheLastJson)
        json.draw = request.draw
        data = json[json.collection_name]
        data.splice(0, requestStart-cacheLower)
        data.splice(requestLength, data.length);
        settings.sAjaxDataProp = json.collection_name
        drawCallback(json)
)

((router) ->
  router.addRoutePattern 'loadTranslationDatatable', $('#contextual-informations').data('datatableTranslationUrlPattern')
) window.appRouter
