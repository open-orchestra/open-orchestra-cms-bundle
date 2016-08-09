###*
 * @channel Block
 * Event available
 *  - moveBlock(blockId, area) move block in area
###
(($, OpenOrchestra) ->

  OpenOrchestra.Page.Block = {} if not OpenOrchestra.Page.Block?
  OpenOrchestra.Page.Block.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
