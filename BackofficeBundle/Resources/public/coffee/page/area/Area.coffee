###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Area or= {}

###*
 * @class Area
###
class OpenOrchestra.Page.Area.Area extends Backbone.Model

  ###*
   * set nested attributes area and blocks
  ###
  set: (models) ->
    super(models)
    @attributes.areas = new OpenOrchestra.Page.Area.AreaCollection(models.areas) if models.areas?
    @attributes.blocks = new OpenOrchestra.Page.Block.BlockCollection(models.blocks) if models.blocks?

  ###*
   * Add Block in area
  ###
  addBlock: (block) ->
    @attributes.blocks.push(block)
    @updateBlock()
    return

  ###*
   * Remove Block in area
  ###
  removeBlock: (block) ->
    @attributes.blocks.remove(block)
    @updateBlock(false)
    return

  ###*
   * update order of blockCollection
  ###
  updateOrderBlocks: (orderBlocksId) ->
    blocks = new OpenOrchestra.Page.Block.BlockCollection
    for blockId in orderBlocksId
      blocks.add(@attributes.blocks.get(blockId))
    @attributes.blocks = blocks

  ###*
   * Flush blocks
   *
   * @param {boolean} trigger Trigger the update
  ###
  updateBlock: (trigger = true) ->
    viewContext = @
    $.ajax
      url: @attributes.links._self_update_block
      method: 'POST'
      data: JSON.stringify @toJSON()
      success: ->
        if trigger
          OpenOrchestra.Page.Area.Channel.trigger 'updateArea', viewContext.get('area_id')
    return

  ###*
   * Transform area in json
  ###
  toJSON: () ->
    area = {}
    area.area_id = @attributes.area_id

    blocks = []
    for block in @attributes.blocks.models
      blocks.push(block.toJSON())
    area.blocks = blocks

    return area

