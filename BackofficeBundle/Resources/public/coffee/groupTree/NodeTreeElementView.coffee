NodeTreeElementView = OrchestraView.extend(
  initialize: (options) ->
    @options = options

    # This view not needed groupTreeForm template, there is loaded here to optimize number of request
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/nodeTreeElement',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/groupTreeForm',
    ]
    return

  render: ->
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/nodeTreeElement',
      nodes: @options.nodes
    )
    @formInput = @options.domContainer.find('div.form-input').last()
    nodeId = @options.nodes.node.node_id
    nodeGroupRoles = @options.group.model_roles.filter (element) ->
      element.model_id == nodeId
    formCollectionViewClass = appConfigurationView.getConfiguration('group_tab_node_tree_form', 'editEntityTab')
    roles = @options.roles.roles

    if nodeId == 'root'
      # A root node can't be deleted
      roles = @options.roles.roles.filter (element) ->
        element.name != "ROLE_ACCESS_DELETE_NODE"

    new formCollectionViewClass(
      roles: roles
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
