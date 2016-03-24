###*
 * @channel DataTable
 * Event available
 *  - removeEntity(tableId) remove an entity of table
###
(($, OpenOrchestra) ->

  OpenOrchestra.Table = {} if not OpenOrchestra.Table?
  OpenOrchestra.Table.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
