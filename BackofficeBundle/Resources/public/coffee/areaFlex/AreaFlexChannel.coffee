###*
 * @channel AreaFlex
 * Event available
 *  - activateEditArea(areaId) enable edition of an area
###
(($, OpenOrchestra) ->

  OpenOrchestra.AreaFlex = {} if not OpenOrchestra.AreaFlex?
  OpenOrchestra.AreaFlex.Channel = new (Backbone.Wreqr.EventAggregator)

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
