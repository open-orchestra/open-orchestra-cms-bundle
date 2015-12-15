###*
 * @channel DataTable
 * Event available
 *  - clearCache(tableId) clearCache of DataTable
 *  - draw(tableId)
###
(($, OpenOrchestra) ->
  OpenOrchestra.DataTable = {} if not OpenOrchestra.DataTable?
  OpenOrchestra.DataTable.Channel = new (Backbone.Wreqr.EventAggregator)
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
