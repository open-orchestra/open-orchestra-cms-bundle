TableviewCollectionView = OrchestraView.extend(
  events:
    'click #none': 'clickAdd'

  initialize: (options) ->
    @options = options
    @options.order = [ 0, 'asc' ]
    @options.order = options.order if options.order != undefined
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
    $(@el).html @renderTemplate('tableviewCollectionView',
      displayedElements: @options.displayedElements
      links: @options.elements.get('links')
      cid: @cid
    )
    $('.js-widget-title', @$el).text @options.title
    for element of @options.elements.get(@options.elements.get('collection_name'))
      @addElementToView (@options.elements.get(@options.elements.get('collection_name'))[element])
    $('#tableviewCollectionTable').dataTable(
      searching: false
      ordering: true
      order: [@options.order]
      lengthChange: false
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
