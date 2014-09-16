NodeView = Backbone.View.extend(
  el: '#content'
  events:
    'click i#none' : 'clickButton'
  initialize: (options) ->
    @node = options.node
    key = "click i." + @node.cid
    @events[key] = "clickButton"
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @nodeTemplate = _.template($("#nodeView").html())
    @render()
    nav_page_height()
    return
  clickButton: (event) ->
    $('.modal-title').text @node.get('name')
    displayLoader('.modal-body')
    $.ajax
      url: @node.get('links')._self_form
      method: 'GET'
      success: (response) ->
        view = new adminFormView(html: response)
  render: ->
    $(@el).html @nodeTemplate(
      node: @node
    )
    $('.js-widget-title', @$el).text @node.get('name')
    areaHeight = 100 / @node.get('areas').length if @node.get('areas').length > 0
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area], areaHeight)
    return
  addAreaToView: (area, areaHeight) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      height: areaHeight
    )
    this.$el.find('div[role="container"]').children('div').children('ul.ui-model-areas').append areaView.render().el
    return
)
