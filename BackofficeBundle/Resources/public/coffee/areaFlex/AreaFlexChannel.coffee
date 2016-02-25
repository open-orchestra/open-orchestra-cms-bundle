###*
 * @channel AreaFlex
 * Event available
 *  - activateEditArea(areaId) enable edition of an area
 *  - activateSortableArea(containerAreaId, areaViewSortable) enable sortable area in a container
 *  - disableSortableArea() disable sortable in area
###
(($, OpenOrchestra) ->

  OpenOrchestra.AreaFlex = {} if not OpenOrchestra.AreaFlex?
  OpenOrchestra.AreaFlex.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
