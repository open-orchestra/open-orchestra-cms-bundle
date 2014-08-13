AreaView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-areas'
  initialize: (options) ->
    @area = options.area
    @height = options.height
    @direction = options.direction || 'height'
    @displayClass = (if @direction is "width" then "inline" else "block")
    _.bindAll this, "render", "addAreaToView", "addBlockToView"
    @areaTemplate = _.template($('#areaView').html())
    return
  render: ->
    $(@el).attr('style', @direction + ':' + @height + '%').addClass(@displayClass).html @areaTemplate(
      area: @area
    )
    if @area.get('bo_direction') is 'v'
      childrenDirection = 'width'
    else
      childrenDirection = 'height'
    areaHeight = 100 / @area.get('areas').length if @area.get('areas').length > 0
    blockHeight = 100 / @area.get('blocks').length if @area.get('blocks').length > 0
    for area of @area.get('areas')
      @addAreaToView(@area.get('areas')[area], areaHeight, childrenDirection)
    for block of @area.get('blocks')
      @addBlockToView(@area.get('blocks')[block], blockHeight)
    $("ul.ui-model-blocks", @el).remove() if $("ul.ui-model-blocks", @el).children().length == 0
    $("ul.ui-model-areas", @el).remove() if $("ul.ui-model-areas", @el).children().length == 0
    this
  addAreaToView: (area, areaHeight, childrenDirection) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      height: areaHeight
      direction: childrenDirection
    )
    $("ul.ui-model-areas", @el).append areaView.render().el
    return
  addBlockToView: (block, blockHeight) ->
    blockElement = new Block
    blockElement.set block
    blockView = new BlockView(
      block: blockElement
      height: blockHeight
    )
    $("ul.ui-model-blocks", @el).append blockView.render().el
)
