BlockView = OrchestraView.extend(
  initialize: (options) ->
    @events = {}
    @events['click span.block-param-edit-' + @cid] = 'paramBlockEdit'
    @events['click span.block-param-view-' + @cid] = 'paramBlockView'
    @block = options.block
    @areaCid = options.areaCid
    @node_published = options.node_published
    $(@el).addClass options.displayClass
    _.bindAll this, "render"
    @loadTemplates [
        "blockView"
    ]
    return

  paramBlockEdit: (event) ->
    @paramBlock(event, false)

  paramBlockView: (event) ->
    @paramBlock(event, true)

  paramBlock: (event, disabled) ->
    $('.modal-title').text 'Please wait ...'
    view = new adminFormView(
      url: @block.get('links')._self_form
      disabled: disabled
    )

  render: ->
    $(@el).append @renderTemplate('blockView',
      block: @block
      cid: @cid
      areaCid: @areaCid
      node_published: @node_published
    )
    this
)
