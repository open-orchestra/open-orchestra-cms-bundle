AreaView = OrchestraView.extend(
  events:
    'click span.area-param': 'paramArea'
    'click span.area-remove': 'confirmRemoveArea'
    'sortupdate ul.ui-model-blocks': 'sendBlockData'

  initialize: (options) ->
    @options = @reduce(options, [
      'area'
      'height'
      'node_id'
      'node_published'
      'domContainer'
      'viewContainer'
    ])
    @loadTemplates [
      "areaView"
    ]
    return

  render: ->
    @setElement @renderTemplate('areaView',
      area: @options.area
      node_published: @options.node_published
    )
    @options.domContainer.append @$el
    @subAreas = @$el.find('ul.ui-model-areas').first()
    @subBlocks = @$el.find('ul.ui-model-blocks').first()
    @drawContent()

  drawContent: ->
    if @options.area.get("areas").length == 0
      @$el.addClass('area-leaf')
    else
      for area of @options.area.get("areas")
        @addAreaToView @options.area.get("areas")[area]
    for block of @options.area.get("blocks")
      @addBlockToView @options.area.get("blocks")[block]
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
    view = new adminFormView(
      url: @options.area.get("links")._self_form
      deleteurl: @options.area.get("links")._self_delete
      confirmtext: @options.viewContainer.$el.data('delete-confirm-txt')
    )
    return

  addAreaToView: (area) ->
    areaElement = new Area()
    areaElement.set area
    areaView = new AreaView($.extend({}, @options,
      area: areaElement
      domContainer: @subAreas
      viewContainer: @
    ))
    @subAreas.addClass (if @options.area.get("bo_direction") is "h" then "bo-row" else "bo-column")

  addBlockToView: (block) ->
    blockElement = new Block()
    blockElement.set block
    new BlockView($.extend({}, @options,
      block: blockElement
      domContainer: @subBlocks
      viewContainer: @
    ))
    @subBlocks.addClass (if @options.area.get("bo_direction") is "h" then "bo-row" else "bo-column")

  sendBlockData: (event)->
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
