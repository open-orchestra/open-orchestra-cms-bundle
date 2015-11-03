NodeTreeElementView = OrchestraView.extend(
  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTreeElement',
    ]
    return

  render: ->
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTreeElement',
      node: @options.nodes.node
    )
    @formInput = @options.domContainer.find('div.form-input').last()
    nodeId = @options.nodes.node.node_id
    nodeGroupRoles = @options.group.node_roles.filter (element) ->
      element.node == nodeId
    formCollectionViewClass = appConfigurationView.getConfiguration('group_tab_node_tree_form', 'editEntityTab')
    new formCollectionViewClass(
      roles: @options.roles.roles
      domContainer: @formInput
      nodeGroupRoles: nodeGroupRoles
      group: @options.group
      nodeElement: @options.nodes.node
    )
    @subNode = @options.domContainer.find('ul.child-node').last()
    if @options.nodes.childs.length > 0
      for child of @options.nodes.childs
        @addChildToView @options.nodes.childs[child]

  addChildToView: (child) ->
    nodeTreeElementViewClass = appConfigurationView.getConfiguration('group_tab_node_tree_element', 'editEntityTab')
    new nodeTreeElementViewClass(
      group: @options.group
      nodes: child
      domContainer: @subNode
      roles: @options.roles
    )
)

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree_element', 'editEntityTab', NodeTreeElementView
