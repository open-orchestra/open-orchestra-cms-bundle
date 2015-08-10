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
      'editable'
    ])
    @loadTemplates [
        "OpenOrchestraBackofficeBundle:BackOffice:Underscore/blockView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/blockView',
      block: @options.block
      editable: @options.editable
    )
    @options.domContainer.append @$el
    return

  paramBlock: (event) ->
    adminFormViewClass = appConfigurationView.getConfiguration('area', 'showAdminForm')
    new adminFormViewClass(
      url: @options.block.get('links')._self_form
      extendView: [ 'showVideo' ]
      entityType: 'block'
    )

  confirmRemoveBlock: (event) ->
    if @options.area.get("blocks").length > 0
      smartConfirm(
        'fa-trash-o',
        @$el.data('delete-confirm-question'),
        @$el.data('delete-confirm-explanation'),
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
