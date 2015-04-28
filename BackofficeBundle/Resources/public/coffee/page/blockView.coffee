BlockView = OrchestraView.extend(
  events:
    'click span.block-param': 'paramBlock'
    'click i.block-remove': 'confirmRemoveBlock'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'block'
      'area'
      'domContainer'
      'viewContainer'
      'node_published'
    ])
    @loadTemplates [
        "blockView"
    ]
    return

  render: ->
    @setElement @renderTemplate('blockView',
      block: @options.block
      node_published: @options.node_published
    )
    @options.domContainer.append @$el
    return

  paramBlock: (event) ->
    $('.modal-title').text 'Please wait ...'
    new adminFormView(
      url: @options.block.get('links')._self_form
    )

  confirmRemoveBlock: (event) ->
    if @options.area.get("blocks").length > 0
      smartConfirm(
        'fa-trash-o',
        @options.viewContainer.$el.data('delete-confirm-question-block'),
        @options.viewContainer.$el.data('delete-confirm-explanation-block'),
        callBackParams:
          blockView: @
        yesCallback: (params) ->
          params.blockView.removeBlock(event)
      )

  removeBlock: (event) ->
    ul = $(event.target).parents("ul").first()
    $(event.target).parents("li").first().remove()
    refreshUl ul
    @options.viewContainer.sendBlockData({target: ul})
)
