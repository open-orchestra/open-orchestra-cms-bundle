FormCollectionView = OrchestraView.extend(
  events:
    'change .value-holder': 'changeInput'
  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTreeForm',
    ]

  render: ->
    for role in @options.roles
      @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTreeForm',
        role: role
      )
    @setElement @options.domContainer
    if @options.nodeGroupRoles != undefined
      for nodeGroupRole in @options.nodeGroupRoles
        $('[value="' + nodeGroupRole.name + '"]', @options.domContainer).prop('checked', nodeGroupRole.is_granted)

  changeInput: (e) ->
    value = $(e.currentTarget).prop('checked')
    name = $(e.currentTarget).prop('value')
    nodeId = @options.nodeElement.node_id
    nodeGroupRoleData = []
    nodeGroupRoleData.push({'node': nodeId, 'is_granted': value, 'name': name})
    $.ajax
      url: @options.group.links._self_edit
      method: 'POST'
      data: JSON.stringify(
        node_roles: nodeGroupRoleData
      )
)

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree_form', 'editEntityTab', FormCollectionView
