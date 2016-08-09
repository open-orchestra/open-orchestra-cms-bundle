###*
 * @namespace OpenOrchestra:Page:Template
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Template or= {}

###*
 * @class Node
###
class OpenOrchestra.Page.Template.Template extends Backbone.Model

  ###*
   * set nested attributes area and blocks
  ###
  set: (models) ->
    super(models)
    @attributes.root_area = new OpenOrchestra.Page.Area.Area(models.root_area) if models.root_area?
    @attributes.blocks = new OpenOrchestra.Page.Block.BlockCollection(models.blocks) if models.blocks?