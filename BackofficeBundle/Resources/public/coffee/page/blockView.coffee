BlockView = OrchestraView.extend(
  initialize: (options) ->
    @events = {}
    @events['click span.block-param-' + @cid] = 'paramBlock'
    @block = options.block
    @area = options.area
    @node_published = options.node_published
    $(@el).addClass options.displayClass
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
    html = @renderTemplate('blockView',
      block: @block
      cid: @cid
      areaCid: @area.cid
      node_published: @node_published
    )
    content = $(html)
    $(@el).append content
    @setElement content.first()[0]
    this
)
