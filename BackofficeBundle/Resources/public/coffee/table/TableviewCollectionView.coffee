TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'
    'keyup input.search-column': 'searchColumn'
    'page.dt table': 'changePage'

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

    $.fn.dataTable.Api.register('clearPipeline()', ->
      return this.iterator( 'table', (settings) ->
        settings.clearCache = true;
      );
    );
    $.fn.dataTable.pipeline = @dataTablePipeline

    @options.table = $('#tableviewCollectionTable').dataTable(
      searching: true
      ordering: true
      processing: true
      serverSide: true
      bAutoWidth: false
      ajax : $.fn.dataTable.pipeline(
        url : @options.url
        pages: 5
      )
      initComplete: (settings, json) ->
        viewContext.renderAddButton(viewContext, json.links, this)
        page = parseInt(viewContext.options.page) - 1
        if page? and page <= this.api().page.info().pages
          this.api().page(page).draw(false)
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

    api = @options.table.api()
    headers = api.columns().header().toArray()
    for i of headers
      if @options.visibleElements.length > 0 && $(api.column(i).header()).text() != '' && @options.visibleElements.indexOf(i.toString()) == -1
        api.column(i).visible(false)
    @listenToOnce(widgetChannel, 'jarviswidget', @addColvis)
    return

  renderColumnActions : (viewContext, td, cellData, rowData) ->
    elementModel = new TableviewModel
    elementModel.set rowData
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

  addColvis: ->
    colvis = new ($.fn.dataTable.ColVis)(@options.table, exclude: [ @options.displayedElements.length ])
    $('.jarviswidget-ctrls').prepend colvis.button()
)
