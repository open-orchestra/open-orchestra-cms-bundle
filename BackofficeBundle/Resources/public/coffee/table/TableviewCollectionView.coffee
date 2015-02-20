TableviewCollectionView = OrchestraView.extend(
  events:
    'click #none': 'clickAdd'

  initialize: (options) ->
    @elements = options.elements
    @displayedElements = options.displayedElements
    @order = [ 0, 'asc' ]
    @order = options.order if options.order != undefined
    @title = options.title
    @listUrl = options.listUrl
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
      displayedElements: @displayedElements
      links: @elements.get('links')
      cid: @cid
    )
    $('.js-widget-title', @$el).text @title
    for element of @elements.get(@elements.get('collection_name'))
      @addElementToView (@elements.get(@elements.get('collection_name'))[element])
    $('#tableviewCollectionTable').dataTable(
      searching: false
      ordering: true
      order: [@order]
      lengthChange: false
    )
    return

  addElementToView: (elementData) ->
    elementModel = new TableviewModel
    elementModel.set elementData
    view = new TableviewView(
      element: elementModel
      displayedElements: @displayedElements
      title: @title
      listUrl: @listUrl
      el : this.$el.find('tbody')
    )
    return

  clickAdd: (event) ->
    event.preventDefault()
    if $('#main .' + $(event.target).attr('class')).length
      displayLoader('div[role="container"]')
      Backbone.history.navigate('/add')
      title = @title
      listUrl = @listUrl
      $.ajax
        url: @elements.get('links')._self_add
        method: 'GET'
        success: (response) ->
          view = new FullPageFormView(
            html: response
            title: title
            listUrl: listUrl
            element: @elements
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
