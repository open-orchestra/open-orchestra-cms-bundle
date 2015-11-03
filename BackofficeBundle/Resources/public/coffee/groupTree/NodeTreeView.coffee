NodeTreeView = OrchestraView.extend(
  events:
    'click em.fa': 'toggleItemDisplay'
    'click i': 'clickInput'
  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTree',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
    ]
    return

  initializer: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @options.formView = 'editEntityTab'
    @options.domContainer = @$el

  render: ->
    @options.domContainer.html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeTree')
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
      listUrl : @options.listUrl
    )
    currentView = @
    $.ajax
      url: currentView.options.html.links._self_node_tree
      method: "GET"
      success: (response) ->
        currentView.options.nodes = response
        $.ajax
          url: currentView.options.html.links._role_list_node
          method: "GET"
          success: (response) ->
            currentView.options.roles = response
            currentView.renderTreeElement()

  renderTreeElement: ->
    subContainer = @options.domContainer.find('ul').first()
    nodeTreeElementViewClass = appConfigurationView.getConfiguration('group_tab_node_tree_element', 'editEntityTab')
    new nodeTreeElementViewClass(
      group: @options.html
      nodes: @options.nodes
      domContainer: subContainer
      roles: @options.roles
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
