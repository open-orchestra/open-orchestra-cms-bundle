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
    @attributes.area = new OpenOrchestra.Page.Area.Area(models.area) if models.area?
    @attributes.blocks = new BlockCollection(models.blocks) if models.blocks?