AreaView = OrchestraView.extend(
  initialize: (options) ->
    @area = options.area
    @height = options.height
    @node_id = options.node_id
    @displayClass = options.displayClass
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
    paramkey = "click i.area-param-" + @area.cid
    @events[paramkey] = "paramArea"
    sortUpdateKey = "sortupdate ul.blocks-" + @cid
    @events[sortUpdateKey] = "sendBlockData"
    return

  paramArea: (event) ->
    label = "~no label yet~"
    label = @area.get("label")  if @area.get("label") isnt undefined
    $(".modal-title").text "Area : " + label
<<<<<<< HEAD
    view = new adminFormView(url: @area.get("links")._self_form)
    return

  confirmRemoveArea: (event) ->
    @removeArea event  if confirm("Vous êtes sur le point de supprimer une zone. Souhaitez-vous poursuivre cette action ?")

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
        $(".modal-title").text "Block removal"
        $(".modal-body").html "Erreur durant la suppression de la zone, veuillez recharger la page"
        $("#OrchestraBOModal").modal "show"
=======
    view = new adminFormView(
      url: @area.get("links")._self_form
      deleteurl: @area.get("links")._self_delete
      confirmtext: "Vous êtes sur le point de supprimer une zone. Souhaitez-vous poursuivre cette action ?"
    )
>>>>>>> add delete button on area modal
    return

  render: ->
    $(@el).append @renderTemplate('areaView',
      area: @area
      cid: @cid
      displayClass: @displayClass
    )
    this.drawContent()

  drawContent: ->
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
      displayClass: (if @area.get("bo_direction") is "v" then "inline" else "block")
      el: $("ul.areas-" + @cid, @el)
    )

  addBlockToView: (block) ->
    blockElement = new Block()
    blockElement.set block
    blockView = new BlockView(
      block: blockElement
      displayClass: (if @area.get("bo_direction") is "v" then "inline" else "block")
      areaCid: @area.cid
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
      @removeBlock event  if confirm("Vous êtes sur le point de supprimer un bloc. Souhaitez-vous poursuivre cette action ?")

  removeBlock: (event) ->
    ul = $(event.target).parents("ul").first()
    $(event.target).parents("li").first().remove()
    refreshUl ul
    @sendBlockData({target: ul})
)
