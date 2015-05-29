search = (api, rank) ->
  return ->
    api.column(rank).search($(this).val()).draw()
    return

TableviewCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'elements'
      'entityType'
      'translatedHeader'
      'displayedElements'
      'visibleElements'
      'domContainer'
      'order'
      'title'
    ])
    @options.order = [ 0, 'asc' ] if @options.order == undefined
    @addUrl = appRouter.generateUrl('addEntity', entityType: @options.entityType)
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions'
    ]
    return

  render: ->
    parent = $('#nav-'+@options.entityType).parent('[data-type]')
    if (parent.length)
      @options.entityType = parent[0].getAttribute('data-type')

    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewCollectionView',
      displayedElements: @options.translatedHeader
      links: @options.elements.get('links')
    )
    @options.domContainer.html @$el

    $('.js-widget-title', @options.domContainer).text @options.title
    for element of @options.elements.get(@options.elements.get('collection_name'))
      @addElementToView (@options.elements.get(@options.elements.get('collection_name'))[element])

    table = $('#tableviewCollectionTable').dataTable(
      searching: true
      ordering: true
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
        viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'add')
        new viewClass(viewContext.addOption(
          html: response
          extendView: [ 'generateId' ]
        ))
)
