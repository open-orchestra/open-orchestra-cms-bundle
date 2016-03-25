###*
 * @namespace OpenOrchestra:ContentType
###
window.OpenOrchestra or= {}
window.OpenOrchestra.ContentType or= {}

###*
 * @class TableviewCollectionView
###
class OpenOrchestra.ContentType.TableviewCollectionView extends TableviewCollectionView

  ###*
   * Initialize view
  ###
  initialize: (options) ->
    super
    OpenOrchestra.Table.Channel.bind 'removeEntity', @refreshMenu, @

  ###*
   * Refresh menu when entity is removed
  ###
  refreshMenu: () ->
    displayRoute = appRouter.generateUrl "listEntities",
      entityType: @options.entityType
    refreshMenu(displayRoute)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'showTableCollection', OpenOrchestra.ContentType.TableviewCollectionView)
