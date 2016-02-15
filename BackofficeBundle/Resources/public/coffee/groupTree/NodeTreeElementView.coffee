NodeTreeElementView = OrchestraView.extend(
  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/nodeTreeElement',
    ]
    return

  render: ->
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/nodeTreeElement',
      nodes: @options.nodes
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
    @subNode = @options.domContainer.find('ul.child-document').last()
    if @options.nodes.children.length > 0
      for child of @options.nodes.children
        @addChildToView @options.nodes.children[child]

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
