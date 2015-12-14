###*
* @channel DataTable
* Event available
*  - clearCache(tableId) clearCache of DataTable
*  - draw(tableId)
###
(($, OpenOrchestra, DataTable) ->
  DataTable.Channel = new (Backbone.Wreqr.EventAggregator)
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {} ,
  window.OpenOrchestra.DataTable = window.OpenOrchestra.DataTable or {}
