###*
 * @channel Area
 * Event available
 *  - activateEditArea(areaId) enable edition of an area
 *  - updateArea(areaId) update area
 *  - activateSortableArea(containerAreaId, areaViewSortable) enable sortable area in a container
 *  - disableSortableArea() disable sortable in area
###
(($, OpenOrchestra) ->

  OpenOrchestra.Page.Area = {} if not OpenOrchestra.Page.Area?
  OpenOrchestra.Page.Area.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
