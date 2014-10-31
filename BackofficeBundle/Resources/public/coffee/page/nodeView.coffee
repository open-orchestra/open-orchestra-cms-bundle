NodeView = Backbone.View.extend(
  el: '#content'
  events:
    'click i#none' : 'clickButton'
    'change select#selectbox': 'clickOption'
  initialize: (options) ->
    @node = options.node
    @version = options.version
    key = "click i." + @node.cid
    @events[key] = "clickButton"
    key = 'click a.ajax-node-duplicate-' + @node.cid
    @events[key] = 'duplicateNode'
    _.bindAll this, "render", "addAreaToView", "clickButton", "duplicateNode"
    @nodeTemplate = _.template($("#nodeView").html())
    @nodeTitle = _.template($("#nodeTitle").html())
    @render()
    return
  clickButton: (event) ->
    $('.modal-title').text @node.get('name')
    url = @node.get('links')._self_form
    if @node.attributes.alias is ''
      view = new adminFormView(
        url: url
        triggers: [
          {
            event: "keyup input.alias-source"
            name: "refreshAlias"
            fct: refreshAlias
          }
          {
            event: "blur input.alias-dest"
            name: "stopRefreshAlias"
            fct: stopRefreshAlias
          }
        ]
      )
    else
      view = new adminFormView(url: url)
  duplicateNode: (event) ->
    event.preventDefault() #
    viewContext = this
    $.ajax
      url: @node.get('links')._self_duplicate
      method: 'POST'
      success: (response) ->
        Backbone.history.loadUrl(Backbone.history.fragment)
    return
  render: ->
    title = @nodeTitle(node: @node)
    $(@el).html @nodeTemplate(
      node: @node
      title: title
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    $('.js-widget-panel', @$el).html($('#generated-panel', @$el).html()).show()
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    @addVersionToView()
    if @node.attributes.status.published
      $('.ui-model *', @el).unbind()
      $('.js-widget-panel').hide()
    else
      $("ul.ui-model-areas, ul.ui-model-blocks", @$el).each ->
        refreshUl $(this)
    return
  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node.get('node_id'),
      displayClass: (if @node.get("bo_direction") is "v" then "inline" else "block")
    )
    @$el.find('ul.ui-model-areas').first().append  areaView.render().el
    return
  addVersionToView: ->
    viewContext = this
    $.ajax
      type: "GET"
      url: @node.get('links')._self_version
      success: (response) ->
        nodeCollection = new NodeCollectionElement
        nodeCollection.set response
        for nodeVersion of nodeCollection.get('nodes')
          viewContext.addChoiceToSelectBox(nodeCollection.get('nodes')[nodeVersion])
        return
  addChoiceToSelectBox: (nodeVersion) ->
    nodeVersionElement = new Node
    nodeVersionElement.set nodeVersion
    view = new NodeVersionView(
      node: nodeVersionElement
      version: @version
    )
    this.$el.find('select').append view.render()
  clickOption: (event) ->
    Backbone.history.navigate('#node/show/' + @node.get('node_id') + '/' + event.currentTarget.value, {trigger: true})
    return
)
