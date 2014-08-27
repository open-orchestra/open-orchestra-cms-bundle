TableviewView = Backbone.View.extend(
  tagName: 'tr'
  events:
    'click a.ajax-delete': 'clickDelete'
    'click a.ajax-edit': 'clickEdit'
  initialize: (options) ->
    @element = options.element
    @displayedElements = options.displayedElements
    _.bindAll this, "render"
    @elementTemplate = _.template($('#tableviewView').html())
    @actionsTemplate = _.template($('#tableviewActions').html())
    return
  render: ->
    for displayedElement in @displayedElements
      $(@el).append @elementTemplate(
        value: @element.get(displayedElement)
      )
    $(@el).append @actionsTemplate(
      links: @element.get('links')
    )
    this
  clickDelete: (event) ->
    event.preventDefault()
    if confirm('Delete this element ?')
      $.ajax
        url: @element.get('links')._self_delete
        method: 'DELETE'
        success: (response) ->
          return
      @$el.hide()
  clickEdit: (event) ->
    event.preventDefault()
    $('.modal-title').text 'Edit'
    $.ajax
      url: @element.get('links')._self_form
      method: 'GET'
      success: (response) ->
        view = new adminFormView(html: response)
)

TableviewCollectionView = Backbone.View.extend(
  el: '#content'
  events:
    'click a.ajax-add': 'clickAdd'
  initialize: (options) ->
    @elements = options.elements
    @displayedElements = options.displayedElements
    @title = options.title
    _.bindAll this, "render"
    @elementsTemplate = _.template($('#tableviewCollectionView').html())
    @render()
    return
  render: ->
    $(@el).html @elementsTemplate (
      displayedElements: @displayedElements
      links: @elements.get('links')
    )
    $('.js-widget-title', @$el).text @title
    for element of @elements.get(@elements.get('collection_name'))
      @addElementToView (@elements.get(@elements.get('collection_name'))[element])
    $('#tableviewCollectionTable').dataTable(
      searching: false
      ordering: true
      lengthChange: false
    )
    return
  addElementToView: (elementData) ->
    elementModel = new TableviewModel
    elementModel.set elementData
    view = new TableviewView(
      element: elementModel
      displayedElements: @displayedElements
    )
    this.$el.find('tbody').append view.render().el
    return
  clickAdd: (event) ->
    event.preventDefault()
    $('.modal-title').text 'Add'
    $.ajax
      url: @elements.get('links')._self_add
      method: 'GET'
      success: (response) ->
        view = new adminFormView(html: response)
)
