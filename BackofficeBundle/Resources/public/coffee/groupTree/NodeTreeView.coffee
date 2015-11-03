NodeTreeView = OrchestraView.extend(
  tagName: 'ul'
  events:
    'click em.fa': 'toggleItemDisplay'
    'click i': 'clickInput'
  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
    ]
    return

  initializer: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @options.formView = 'editEntityTab'
    @options.domContainer = @$el
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
    nodeTreeElementViewClass = appConfigurationView.getConfiguration('group_tab_node_tree_element', 'editEntityTab')
    new nodeTreeElementViewClass(
      group: @options.html
      nodes: @options.nodes
      domContainer: @$el
      roles: @options.roles
    )
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
        listUrl : @options.listUrl
    )
    $('.fa', @$el).addClass 'fa-minus-square-o'

  toggleItemDisplay: (e) ->
    OpenOrchestra.toggleTreeNodeDisplay e

  clickInput: (e) ->
    inputElement = $(e.target).parent().find('input')
    checked = inputElement.prop('checked')
    $('[value="' + inputElement.prop('value') + '"]', inputElement.closest('li').children('.child-node')).prop('checked', !checked).trigger('change')
)

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_node_tree', 'editEntityTab', NodeTreeView
