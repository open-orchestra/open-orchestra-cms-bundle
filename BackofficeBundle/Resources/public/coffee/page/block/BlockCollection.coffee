###*
 * @namespace OpenOrchestra:Page:Block
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Block or= {}

###*
 * @class BlockCollection
###
class OpenOrchestra.Page.Block.BlockCollection extends Backbone.Collection

  model: OpenOrchestra.Page.Block.Block
