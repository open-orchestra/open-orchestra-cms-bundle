AreaView = OrchestraView.extend(
  extendView : [ 'addArea' ]

  events:
    'click span.area-param': 'paramArea'
    'click span.area-remove': 'confirmRemoveArea'
    'sortupdate ul.ui-model-blocks': 'sendBlockData'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'configuration'
      'published'
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaView', @options)
    @options.domContainer.append @$el
    @subAreas = @$el.find('ul.ui-model-areas').first()
    @subBlocks = @$el.find('ul.ui-model-blocks').first()
    @drawContent()

  drawContent: ->
    if @options.area.get("areas").length == 0
      @$el.addClass('area-leaf')
    else
      @addAreasToView @options.area.get("areas")
    for block of @options.area.get("blocks")
      @addBlockToView @options.area.get("blocks")[block]
    refreshUl @subBlocks
    if @subAreas.children().length is 0
      @subAreas.remove()
      @subBlocks.addClass('bo-column') if @subBlocks.children().length is 0
      makeSortable @el
    else
      @subBlocks.remove()
    return

  purgeContent: ->
    @subAreas.empty()
    @subBlocks.empty()

  paramArea: (event) ->
    event.stopImmediatePropagation()
    label = "~no label yet~"
    label = @options.area.get("label")  if @options.area.get("label") isnt undefined
    $(".modal-title").text "Area : " + label
    new adminFormView(
      url: @options.area.get("links")._self_form
    )
    return

  addBlockToView: (block) ->
    blockElement = new Block()
    blockElement.set block
    new BlockView(@addOption(
      block: blockElement
      domContainer: @subBlocks
      viewContainer: @
    ))
    @subBlocks.addClass (if @options.area.get("bo_direction") is "h" then "bo-row" else "bo-column")

  sendBlockData: (event)->
    event.stopImmediatePropagation() if event.stopImmediatePropagation
    ul = $(event.target)
    refreshUl ul
    blocks = ul.children()
    blockData = []
    for block in blocks
      info = $('div[data-block-type]', block)
      if info.length > 0
        if info.data('node-id') != '' && info.data('block-id') != '' 
          blockData.push({'node_id' : info.data('node-id'), 'block_id' : info.data('block-id')})
        else
          blockData.push({'component' : info.data('block-type')})
    mustRefresh = !! ul.find(".newly-inserted").length > 0
    currentView = @
    $.ajax
      url: @options.area.get('links')._self_block
      method: 'POST'
      data: JSON.stringify(
        blocks: blockData
      )
      success: (response) ->
        currentView.refresh() if mustRefresh

  refresh: ->
    currentView = @
    $.ajax
      url: @options.area.get('links')._self
      method: 'GET'
      success: (response) ->
        currentView.options.area.set(response)
        currentView.purgeContent()
        currentView.drawContent()
        refreshUl(currentView.subBlocks)

  confirmRemoveArea: (event) ->
    event.stopImmediatePropagation()
    smartConfirm(
      'fa-trash-o',
      @$el.data('delete-confirm-question'),
      @$el.data('delete-confirm-explanation'),
      callBackParams:
        areaView: @
      yesCallback: (params) ->
        params.areaView.removeArea()
    )

  removeArea: () ->
    @$el.remove()
    refreshUl @options.domContainer
    @sendRemoveArea()
    return

  sendRemoveArea: ->
    currentView = @
    $.ajax
      url: @options.area.get("links")._self_delete
      method: "POST"
      error: ->
        $(".modal-title").text currentView.$el.data('delete-error-title')
        $(".modal-body").html currentView.$el.data('delete-error-txt')
        $("#OrchestraBOModal").modal "show"
    return
)
