###*
 * @class FormNodeCollectionView
###
class OpenOrchestra.GroupTree.FormNodeCollectionView extends OpenOrchestra.GroupTree.AbstractFormCollectionView
  getElement: ->
    return 'nodeElement'
  getId: ->
    return 'node_id'
  getType: ->
    return 'node'
  getGroupRoles: ->
    return 'nodeGroupRoles'

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree_form', 'editEntityTab', OpenOrchestra.GroupTree.FormNodeCollectionView
