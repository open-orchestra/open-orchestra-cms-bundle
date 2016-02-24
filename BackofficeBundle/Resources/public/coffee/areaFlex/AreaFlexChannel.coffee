###*
 * @channel AreaFlex
 * Event available
 *  - activateEditArea(areaId) enable edition of an area
 *  - activateSortableAreaRow(areaRowId) enable sortable area in a row
 *  - disableSortableArea() disable sortable in area
###
(($, OpenOrchestra) ->

  OpenOrchestra.AreaFlex = {} if not OpenOrchestra.AreaFlex?
  OpenOrchestra.AreaFlex.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
