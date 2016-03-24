###*
 * @namespace OpenOrchestra:ContentType
###
window.OpenOrchestra or= {}
window.OpenOrchestra.ContentType or= {}

###*
 * @class TableViewAction
###
class OpenOrchestra.ContentType.TableViewAction extends TableviewAction

  ###*
   * Initialize view
  ###
  initialize: (options) ->
    super
    OpenOrchestra.Table.Channel.bind 'removeEntity', @refreshMenu, @

  ###*
   * Refresh menu when entity is removed
  ###
  refreshMenu: (tableId) ->
    if tableId == @options.tableId
      displayRoute = appRouter.generateUrl "listEntities",
        entityType: @options.entityType
      refreshMenu(displayRoute)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'addButtonAction', OpenOrchestra.ContentType.TableViewAction)
