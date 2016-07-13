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
    @attributes.area = new OpenOrchestra.Page.Area.Area(models.area) if models.area?
    @attributes.blocks = new BlockCollection(models.blocks) if models.blocks?