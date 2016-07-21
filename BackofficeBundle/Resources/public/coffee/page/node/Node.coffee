###*
 * @namespace OpenOrchestra:Page:Node
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Node or= {}

###*
 * @class Area
###
class OpenOrchestra.Page.Node.Node extends Backbone.Model

  ###*
   * set nested attributes area and blocks
  ###
  set: (models) ->
    super(models)
    @attributes.root_area = new OpenOrchestra.Page.Area.Area(models.root_area) if models.root_area?
    @attributes.blocks = new OpenOrchestra.Page.Block.BlockCollection(models.blocks) if models.blocks?
