###*
 * @namespace OpenOrchestra:Page:Common
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Common or= {}

###*
 * @class AbstractPageView
###
class OpenOrchestra.Page.Common.AbstractPageView extends OrchestraView

  ###*
   * initialize bind channel
  ###
  initialize: () ->
    OpenOrchestra.Page.Area.Channel.bind 'activateSortableArea', @showOverlaySortableArea, @
    OpenOrchestra.Page.Area.Channel.bind 'disableSortableArea', @hideOverlaySortableArea, @

  ###*
   * @param {Object} container Jquery selector
   * @param {Object} areas List of areas to add in container
  ###
  addArea: (container, area) ->
    areaModel = new Area
    areaModel.set area
    areaViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addArea')
    new areaViewClass(
      area: areaModel
      domContainer: container
      toolbarContainer: $('.toolbar-container', @$el)
    )

  ###*
   * Add button configuration page
  ###
  addConfigurationButton: () ->
    pageLayoutButtonViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addPageLayoutButton')
    new pageLayoutButtonViewClass(@addOption(
      viewContainer: @
      deleteUrl: @options.configuration.get('links')._self_delete
      confirmText: @$el.data('delete-confirm-txt')
      confirmTitle: @$el.data('delete-confirm-title')
    ))

  ###*
   * Hide overlay on area
  ###
  hideOverlaySortableArea: () ->
    if $('.overlay-sortable', @$el).length > 0
      $('.overlay-sortable', @$el).remove()

  ###*
   * Show overlay on area
  ###
  showOverlaySortableArea: () ->
    if $('.overlay-sortable', @$el).length == 0
      $('.page-container',@$el).prepend($('<div/>', { 'class': 'overlay-sortable'}))
