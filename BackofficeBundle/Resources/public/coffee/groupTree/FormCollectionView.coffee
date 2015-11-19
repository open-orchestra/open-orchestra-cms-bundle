FormCollectionView = OrchestraView.extend(
  events:
    'change .value-holder': 'changeInput'
  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/groupTreeForm',
    ]

  render: ->
    for role in @options.roles
      @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/groupTreeForm',
        role: role
        node: @options.nodeElement
      )
    @setElement @options.domContainer
    if @options.nodeGroupRoles != undefined
      for nodeGroupRole in @options.nodeGroupRoles
        $('select[data-role-name="' + nodeGroupRole.name + '"] option[value="' + nodeGroupRole.access_type + '"]', @options.domContainer).attr('selected','selected')

  changeInput: (e) ->
    value = $(e.currentTarget).val()
    name = $(e.currentTarget).data('role-name')
    nodeId = @options.nodeElement.node_id
    nodeGroupRoleData = []
    nodeGroupRoleData.push({'node': nodeId, 'access_type': value, 'name': name})
    $.ajax
      url: @options.group.links._self_edit
      method: 'POST'
      data: JSON.stringify(
        node_roles: nodeGroupRoleData
      )
)

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree_form', 'editEntityTab', FormCollectionView
