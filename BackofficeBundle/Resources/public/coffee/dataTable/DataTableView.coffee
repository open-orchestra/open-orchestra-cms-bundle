###*
* @class DataTableView
###
class DataTableView extends OrchestraView

  tagName: 'div'

  events:
    'init.dt table': 'initComplete'
    'processing.dt table': 'processingData'

  defaultSettings:
      serverSide: true
      processing: true
      pageLength: 10
      page: 0
      searching: true
      ordering: true
      orderCellsTop: true
      autoWidth: false
      stateSave: true
      pagingType: 'input_full'
      globalSearch: false
      tableClassName: ''

  ###*
  * required options
  * {
  *   columns: {object}
  *   columnDefs: {object}
  *   tableId : {string}
  * }
  * if serverSide is activate (by default it is enabled),
  * you should also give url option
  *
  * @param {Object} options
  ###
  initialize : (options) ->
    @options = options
    @api = null
    @settings = {}
    OpenOrchestra.DataTable.Channel.bind 'draw', @draw, @
    @loadTemplates ['OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/processing']

  ###*
  * @return {this}
  ###
  render: ->
    settings = $.extend true, {}, @defaultSettings, @options

    $.extend $.fn.dataTableExt.oStdClasses, {
      "sWrapper": "dataTables_wrapper form-inline",
      "sFilterInput": "form-control",
      "sLengthSelect": "form-control"
    }

    if(not settings.displayStart?)
      settings.displayStart = settings.pageLength * settings.page

    if(not settings.buttons?)
       settings.buttons = @getButtonsSettings(settings);

    if(settings.serverSide? and true == settings.serverSide)
        $.extend true, settings, @getServerSideSettings(settings)
        OpenOrchestra.DataTable.Channel.bind 'clearCache', @clearCache, @

    if(settings.stateSave? and true == settings.stateSave)
        $.extend true, settings, @getStateSaveSettings(settings)

    if(not settings.dom?)
      settings.dom = @getDomSettings(settings);

    table = $("<table></table>")
    table.addClass(settings.tableClassName)
    table.attr('id', 'dt-' + settings.tableId)

    @settings = settings
    @api = table.DataTable(settings)

    $(this.el).append(table);

    return @

  ###*
  * @param {object} e jquery event
  * @param {Object} settings DataTable settings
  * @param {boolean} processing
  ###
  processingData : (e, settings, processing) ->
    message = settings.oLanguage.sProcessing
    domProcessing = @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/processing',
        message : message
    )
    if processing
      $('.dataTables_wrapper', @$el).append(domProcessing) if $('.dataTables_processing', @$el).length == 0
      $('.dataTables_processing', @$e).show()
    else
      $('.dataTables_processing', @$el).hide()

  ###*
  * @param {object} e jquery event
  * @param {Object} settings DataTable settings
  * @param {object} json DataTable data retrieved from server
  ###
  initComplete: (e, settings, json) ->
    api = $(e.target).DataTable()
    headerViewClass = appConfigurationView.getConfiguration(@options.tableId,'showTableHeader')
    new headerViewClass(
      api : api
      domContainer : $('thead', @$el)
    )

  ###*
  * @param {Object} settings DataTable settings
  *
  * @return array
  ###
  getButtonsSettings: (settings) ->
    columns = []
    for column in settings.columnDefs
      columns.push column.targets if column.activateColvis
    buttonsSettings = [
      extend: 'colvis'
      columns: columns
      className: 'pull-right'
    ]

    return buttonsSettings;

  ###*
  * Defaults settings dom
  * @param {Object} settings DataTable settings
  *
  * @return string
  ###
  getDomSettings: (settings) ->
    dom = "<'row dt-toolbar'"
    dom += "<'col-xs-12 col-sm-6'f>" if settings.globalSearch
    numberColum = if settings.globalSearch then 5 else 11
    dom += "<'col-sm-"+numberColum+" col-xs-6 hidden-xs'B><'col-xs-12 col-sm-1 hidden-xs'l>>"
    dom += "t"
    dom += "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>"

    return dom

  ###*
  * Defaults settings when state save is enabled
  * @param {Object} settings DataTable settings
  *
  * @return {Object}
  ###
  getStateSaveSettings: (settings) ->
    stateSaveSettings = {}
    viewContext = @
    if not settings.stateSaveCallback?
      stateSaveSettings.stateSaveCallback = (settings, data) ->
        localStorage.setItem 'DataTables_' + viewContext.options.tableId, JSON.stringify(data)
    if not settings.stateLoadCallback?
      stateSaveSettings.stateLoadCallback = (settings) ->
        JSON.parse localStorage.getItem('DataTables_' + viewContext.options.tableId)

    return stateSaveSettings

  ###*
  * Defaults settings when serverSide is enabled
  * @param {Object} settings DataTable settings
  *
  * @return {Object}
  ###
  getServerSideSettings: (settings) ->
    viewContext = @
    $.fn.dataTable.pipeline = @dataTablePipeline
    $.fn.dataTable.Api.register('clearPipeline()', ->
      return this.iterator( 'table', (settings) ->
        settings.clearCache = true;
      )
    )
    serverSideSettings = {}
    if not settings.ajax?
      serverSideSettings.ajax = $.fn.dataTable.pipeline(url : @options.url, pages: 5)
    if not settings.serverParams?
      serverSideSettings.serverParams = (data) ->
        data.search = viewContext.transformerDataSearch(data)
        data.order = viewContext.transformDataOrder(data)
        delete data.columns
        delete data.draw

    return serverSideSettings

  ###*
  * @param {object} data
  ###
  transformerDataSearch: (data) ->
    search =
    columns : {}
    for column in data.columns
      if column.searchable = true and column.search.value != '' and column.name != ''
        name = column.name
        search.columns[name] = column.search.value
    if data.search.value != ''
      search['global'] = data.search.value

    return search

  ###*
  * @param {object} data
  ###
  transformDataOrder: (data) ->
    for order in data.order
      if data.columns[order.column]? and data.columns[order.column].orderable = true
          name = data.columns[order.column].name if data.columns[order.column]?
          dir = order.dir
          return name: name, dir:dir

    return null

  ###
  * @param {string} tableId
  ###
  draw: (tableId) ->
    if tableId == @options.tableId and @api?
        @api.draw()

  ###
  * @param {string} tableId
  ###
  clearCache: (tableId) ->
    if tableId == @options.tableId and @api?
      @api.settings()[0].clearCache = true

  ###*
  * @param {object} opts
  ###
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
        settings.jqXHR.abort() if settings.jqXHR?
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
