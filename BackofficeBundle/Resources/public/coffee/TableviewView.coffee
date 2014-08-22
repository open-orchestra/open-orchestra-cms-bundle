TableviewView = Backbone.View.extend(
  tagName: 'tr'
  initialize: (options) ->
    @element = options.element
    @displayedElements = options.displayedElements
    _.bindAll this, "render"
    @elementTemplate = _.template($('#tableviewView').html())
    return
  render: ->
    for displayedElement in @displayedElements
      $(@el).append @elementTemplate(
        value: @element.get(displayedElement)
      )
    this
)

TableviewCollectionView = Backbone.View.extend(
  el: '#content'
  initialize: (options) ->
    @elements = options.elements
    @displayedElements = options.displayedElements
    _.bindAll this, "render"
    @elementsTemplate = _.template($('#tableviewCollectionView').html())
    @render()
    return
  render: ->
    $(@el).html @elementsTemplate (
      displayedElements: @displayedElements
    )
    for element of @elements.get('sites')
      @addElementToView (@elements.get('sites')[element])
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
)
