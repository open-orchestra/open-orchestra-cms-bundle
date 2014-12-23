AreaView = OrchestraView.extend(
  initialize: (options) ->
    @area = options.area
    @height = options.height
    @node_id = options.node_id
    @node_published = options.node_published
    $(@el).addClass options.displayClass
    @initEvents()
    _.bindAll this, "render", "addAreaToView", "addBlockToView"
    @loadTemplates [
      "areaView"
    ]
    return

  initEvents: ->
    @events = {}
    @events["click i#none"] = "clickButton"
    @events["click i.block-remove-" + @area.cid] = "confirmRemoveBlock"
    @events["click span.area-param-" + @area.cid] = "paramArea"
    @events["click span.area-remove-" + @area.cid] = "confirmRemoveArea"
    sortUpdateKey = "sortupdate ul.blocks-" + @cid
    @events[sortUpdateKey] = "sendBlockData"
    return

  paramArea: (event) ->
    label = "~no label yet~"
    label = @area.get("label")  if @area.get("label") isnt undefined
    $(".modal-title").text "Area : " + label
    view = new adminFormView(
      url: @area.get("links")._self_form
      deleteurl: @area.get("links")._self_delete
      confirmtext: $(".delete-confirm-txt-"+@cid).text()
    )
    return

  render: ->
    $(@el).append @renderTemplate('areaView',
      area: @area
      cid: @cid
      node_published: @node_published
    )
    this.drawContent()

  drawContent: ->
    if @area.get("areas").length == 0
      $(@el).find('#area-' + @cid).addClass('area-leaf')
    else
      for area of @area.get("areas")
        @addAreaToView @area.get("areas")[area]
    
    for block of @area.get("blocks")
      @addBlockToView @area.get("blocks")[block]
    
    if $("ul.areas-" + @cid, @el).children().length is 0
      $("ul.areas-" + @cid, @el).remove()
      makeSortable @el
    else
      $("ul.blocks-" + @cid, @el).remove() if $("ul.blocks-" + @cid, @el).children().length is 0
    
    this

  purgeContent: ->
    $("ul.areas-" + @cid, @el).empty()
    $("ul.blocks-" + @cid, @el).empty()

  addAreaToView: (area) ->
    areaElement = new Area()
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node_id
      node_published: @node_published
      displayClass: (if @area.get("bo_direction") is "h" then "bo-row" else "bo-column")
      el: $("ul.areas-" + @cid, @el)
    )

  addBlockToView: (block) ->
    blockElement = new Block()
    blockElement.set block
    blockView = new BlockView(
      block: blockElement
      displayClass: (if @area.get("bo_direction") is "h" then "bo-row" else "bo-column")
      areaCid: @area.cid
      node_published: @node_published
      el: $("ul.blocks-" + @cid, @el)
    )

  sendBlockData: (event)->
    ul = $(event.target)
    refreshUl ul
    blocks = ul.children()
    blockData = []
    for block in blocks
      if $('div[data-node-id]', block).length > 0
        blockData.push({'node_id' : $('div[data-node-id]', block)[0].getAttribute('data-node-id'), 'block_id' : $('div[data-block-id]', block)[0].getAttribute('data-block-id')})
      else if $('div[data-block-type]', block).length > 0
        blockData.push({'component' : $('div[data-block-type]', block)[0].getAttribute('data-block-type')})
    areaData = {}
    areaData['blocks'] = blockData
    mustRefresh = !! ul.find(".newly-inserted").length > 0
    currentView = this
    $.ajax
      url: @area.get('links')._self_block
      method: 'POST'
      data: JSON.stringify(areaData)
      success: (response) ->
        currentView.refresh() if mustRefresh

  refresh: ->
    currentView = this
    $.ajax
      url: @area.get('links')._self
      method: 'GET'
      success: (response) ->
        currentView.area.set(response)
        currentView.purgeContent()
        currentView.drawContent()
        refreshUl $("ul.blocks-" + currentView.cid, currentView.el)

  confirmRemoveBlock: (event) ->
    if @area.get("blocks").length > 0
      smartConfirm(
        'fa-trash-o',
        'Delete this block',
        'The removal will be final',
        callBackParams:
          blockView: @
        yesCallback: (params) ->
          params.blockView.removeBlock(event)
      )

  removeBlock: (event) ->
    ul = $(event.target).parents("ul").first()
    $(event.target).parents("li").first().remove()
    refreshUl ul
    @sendBlockData({target: ul})

  confirmRemoveArea: (event) ->
    smartConfirm(
      'fa-trash-o',
      $(".delete-confirm-question-" + @cid).text(),
      $(".delete-confirm-explanation-" + @cid).text(),
      callBackParams:
        areaView: @
      yesCallback: (params) ->
        params.areaView.removeArea(event)
    )

  removeArea: (event) ->
    $(event.target).closest('li').remove()
    refreshUl $(@el)
    @sendRemoveArea()
    return

  sendRemoveArea: ->
    $.ajax
      url: @area.get("links")._self_delete
      method: "POST"
      error: ->
        $(".modal-title").text $(".delete-error-title-" + @cid).text()
        $(".modal-body").html $(".delete-error-txt-" + @cid).text()
        $("#OrchestraBOModal").modal "show"
    return
)
