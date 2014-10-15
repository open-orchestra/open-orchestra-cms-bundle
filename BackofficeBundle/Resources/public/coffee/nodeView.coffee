NodeView = Backbone.View.extend(
  el: '#content'
  events:
    'click i#none' : 'clickButton'
  initialize: (options) ->
    @node = options.node
    key = "click i." + @node.cid
    @events[key] = "clickButton"
    key = 'click a.ajax-node-duplicate-' + @node.cid
    @events[key] = 'duplicateNode'
    _.bindAll this, "render", "addAreaToView", "clickButton", "duplicateNode"
    @nodeTemplate = _.template($("#nodeView").html())
    @render()
    nav_page_height()
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
    return
  render: ->
    $(@el).html @nodeTemplate(
      node: @node
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    $('.js-widget-tools', @$el).html $('#generated-tools', @$el).html()
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    return
  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node.get('node_id'),
      displayClass: (if @node.get("bo_direction") is "v" then "inline" else "block")
    )
    this.$el.find('div[role="container"]').children('div').children('ul.ui-model-areas').append areaView.render().el
    $("ul.ui-model-areas, ul.ui-model-blocks", @$el).each ->
      refreshUl $(this)
    return
)
