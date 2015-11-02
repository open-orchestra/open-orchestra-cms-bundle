NodeTreeView = OrchestraView.extend(
  tagName: 'ul'
  events:
    'click em.fa': 'toggleItemDisplay'
    'click i': 'clickInput'
  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTree',
    ]
    return

  initializer: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @options.formView = 'editEntityTab'
    @options.returnButton = true if !@options.domContainer
    @options.domContainer = @$el if !@options.domContainer
    if @options.nodes == undefined
      nodes = {};
      $.ajax
        url: @options.html.links._self_node_tree
        method: "GET"
        async: false
        success: (response) ->
          nodes = response
      @options.nodes = nodes
    if @options.roles == undefined
      roles = {};
      $.ajax
        url: @options.html.links._role_list_node
        method: "GET"
        async: false
        success: (response) ->
          roles = response
      @options.roles = roles

  render: ->
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTree',
        node: @options.nodes.node
    )
    @formInput = @options.domContainer.find('div.form-input').last()
    nodeId = @options.nodes.node.node_id
    nodeGroupRoles = @options.html.node_roles.filter (element) ->
      element.node == nodeId
    new FormCollectionView(
      roles: @options.roles.roles
      domContainer: @formInput
      nodeGroupRoles: nodeGroupRoles
      group: @options.html
      nodeElement: @options.nodes.node
    )
    @subNode = @options.domContainer.find('ul.child-node').last()
    if @options.nodes.childs.length > 0
      for child of @options.nodes.childs
        @addChildToView @options.nodes.childs[child]
    if @options.returnButton
      @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
          listUrl : @options.listUrl
      )
    $('.fa', @$el).addClass 'fa-minus-square-o'

  addChildToView: (child) ->
    new NodeTreeView(
      html: @options.html
      nodes: child
      listUrl: @options.listUrl
      domContainer: @subNode
      roles: @options.roles
    )

  toggleItemDisplay: (e) ->
    OpenOrchestra.toggleTreeNodeDisplay e

  clickInput: (e) ->
    inputElement = $(e.target).parent().find('input')
    checked = inputElement.prop('checked')
    $('[value="' + inputElement.prop('value') + '"]', inputElement.closest('li').children('.child-node')).prop('checked', !checked).trigger('change')
)

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree', 'editEntityTab', NodeTreeView