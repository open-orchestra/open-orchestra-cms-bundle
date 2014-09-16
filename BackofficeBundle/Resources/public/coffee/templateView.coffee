TemplateView = Backbone.View.extend(
  el: '#content'
  events:
    'click i#none' : 'clickButton'
  initialize: (options) ->
    @template = options.template
    key = "click i." + @template.cid
    @events[key] = "clickButton"
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @templateTemplate = _.template($("#templateView").html())
    @render()
    nav_page_height()
    return
  clickButton: (event) ->
    $('.modal-title').text @template.get('name')
    displayLoader('.modal-body')
    $.ajax
      url: @template.get('links')._self_form
      method: 'GET'
      success: (response) ->
        view = new adminFormView(html: response)
  render: ->
    $(@el).html @templateTemplate(
      template: @template
    )
    $('.js-widget-title', @$el).text @template.get('name')
    if @template.get('bo_direction') is 'v'
      childrenDirection = 'width'
    else
      childrenDirection = 'height'
    areaHeight = 100 / @template.get('areas').length if @template.get('areas').length > 0
    for area of @template.get('areas')
      @addAreaToView(@template.get('areas')[area], areaHeight, childrenDirection)
    return
  addAreaToView: (area, areaHeight, childrenDirection) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      height: areaHeight
      direction: childrenDirection
    )
    this.$el.find('div[role="container"]').children('div').children('ul.ui-model-areas').append areaView.render().el
    return
)
