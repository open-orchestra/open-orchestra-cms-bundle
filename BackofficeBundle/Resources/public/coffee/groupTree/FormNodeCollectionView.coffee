###*
 * @namespace OpenOrchestra:GroupTree
###
window.OpenOrchestra or= {}
window.OpenOrchestra.GroupTree or= {}

###*
 * @class FormNodeCollectionView
###
class OpenOrchestra.GroupTree.FormNodeCollectionView extends OpenOrchestra.GroupTree.AbstractFormCollectionView
  ###*
   * getElement
  ###
  getElement: ->
    return 'nodeElement'
  ###*
   * getId
  ###
  getId: ->
    return 'node_id'
  ###*
   * getType
  ###
  getType: ->
    return 'node'
  ###*
   * getGroupRoles
  ###
  getGroupRoles: ->
    return 'nodeGroupRoles'

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree_form', 'editEntityTab', OpenOrchestra.GroupTree.FormNodeCollectionView
