AreaView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-areas'
  events:
    'click i#none' : 'clickButton'
    'sortupdate ul.ui-model-blocks': 'updateBlockSize'
    'click i.block-remove': 'confirmRemoveBlock'
  initialize: (options) ->
    @area = options.area
    key = "click i." + @area.cid
    @events[key] = "clickButton"
    @height = options.height
    @direction = options.direction || 'height'
    @displayClass = (if @direction is "width" then "inline" else "block")
    _.bindAll this, "render", "addAreaToView", "addBlockToView", "clickButton"
    @areaTemplate = _.template($('#areaView').html())
    return
  clickButton: (event) ->
    $('.modal-title').text @area.get('area_id')
    view = new adminFormView(url: @area.get('links')._self_form)
  render: ->
    if @area.get('bo_direction') is 'v'
      @childrenDirection = 'width'
    else
      @childrenDirection = 'height'
    $(@el).attr('style', @direction + ':' + @height + '%').addClass(@displayClass).html @areaTemplate(
      area: @area
      cid: @cid
    )
    areaHeight = 100 / @area.get('areas').length if @area.get('areas').length > 0
    blockHeight = 100 / @area.get('blocks').length if @area.get('blocks').length > 0
    for area of @area.get('areas')
      @addAreaToView(@area.get('areas')[area], areaHeight)
    for block of @area.get('blocks')
      @addBlockToView(@area.get('blocks')[block], blockHeight)
    $("ul.ui-model-blocks", @el).remove() if $("ul.ui-model-blocks", @el).children().length == 0
    if $("ul.ui-model-areas", @el).children().length == 0
      $("ul.ui-model-areas", @el).remove()
      $("ul.ui-model-blocks", @el).sortable(connectWith: "ul.ui-model-blocks").disableSelection()
    this
  addAreaToView: (area, areaHeight) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      height: areaHeight
      direction: @childrenDirection
    )
    $("ul.ui-model-areas", @el).append areaView.render().el
    return
  addBlockToView: (block, blockHeight) ->
    blockElement = new Block
    blockElement.set block
    blockView = new BlockView(
      block: blockElement
      height: blockHeight
      direction: @childrenDirection
    )
    $("ul.ui-model-blocks", @el).append blockView.render().el
  updateBlockSize: ->
    numberOfBlocks = $("ul.resizable-" + @cid, @el).children().length
    if numberOfBlocks > 0
      size = 100 / numberOfBlocks
      $("li", "ul.resizable-" + @cid).attr('style', @childrenDirection + ':' + size + '%')
      if @childrenDirection == 'width'
        $("li.block", "ul.resizable-" + @cid).removeClass('block').addClass('inline')
      else
        $("li.inline", "ul.resizable-" + @cid).removeClass('inline').addClass('block')
    @sendBlockData()
  sendBlockData: ->
    if $("ul.ui-model-areas", @el).length == 0
      blocks = $("ul.resizable-" + @cid, @el).children()
      blockData = []
      for block in blocks
        blockData.push({'node_id' : $('div[data-node-id]', block)[0].getAttribute('data-node-id'), 'block_id' : $('div[data-block-id]', block)[0].getAttribute('data-block-id')})
      areaData = {}
      areaData['blocks'] = blockData
      $.ajax
        url: @area.get('links')._self_block
        method: 'POST'
        data: JSON.stringify(areaData)
  confirmRemoveBlock: (event) ->
    if confirm 'Vous êtes sur le point de supprimer un bloc. Souhaitez-vous poursuivre cette action ?'
      @removeBlock event
  removeBlock: (event) ->
    event.currentTarget.parentNode.parentNode.parentNode.remove()
    @updateBlockSize()
)
