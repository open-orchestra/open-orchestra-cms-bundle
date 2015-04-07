BlockView = OrchestraView.extend(
  initialize: (options) ->
    @events = {}
    @events['click span.block-param-' + @cid] = 'paramBlock'
    @block = options.block
    @area = options.area
    @domContainer = options.domContainer
    @node_published = options.node_published
    _.bindAll this, "render"
    @loadTemplates [
        "blockView"
    ]
    return

  paramBlock: (event) ->
    $('.modal-title').text 'Please wait ...'
    view = new adminFormView(
      url: @block.get('links')._self_form
    )

  render: ->
    @setElement @renderTemplate('blockView',
      block: @block
      cid: @cid
      areaCid: @area.cid
      node_published: @node_published
    )
    @domContainer.append @$el
    this
)
