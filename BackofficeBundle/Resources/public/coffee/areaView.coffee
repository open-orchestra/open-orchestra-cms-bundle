AreaView = Backbone.View.extend(
  tagName: "li"
  className: "ui-model-areas"
  events:
    "click i#none": "clickButton"
    "click i.block-remove": "confirmRemoveBlock"

  initialize: (options) ->
    @area = options.area
    @height = options.height
    @node_id = options.node_id
    @displayClass = options.displayClass
    @areaTemplate = _.template($("#areaView").html())
    paramkey = "click i.area-param-" + @area.cid
    @events[paramkey] = "paramArea"
    removekey = "click i.area-remove-" + @area.cid
    @events[removekey] = "confirmRemoveArea"
    _.bindAll this, "render", "addAreaToView", "addBlockToView"
    return

  paramArea: (event) ->
    label = "~no label yet~"
    label = @area.get("label")  if @area.get("label") isnt undefined
    $(".modal-title").text "Area : " + label
    view = new adminFormView(url: @area.get("links")._self_form)
    return

  confirmRemoveArea: (event) ->
    @removeArea event  if confirm("Vous êtes sur le point de supprimer une zone. Souhaitez-vous poursuivre cette action ?")

  removeArea: (event) ->
    ul = @el.parent()
    $(@el).remove()
    refreshUl ul
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
    return

  render: ->
    $(@el).addClass(@displayClass).html @areaTemplate(
      area: @area
      cid: @cid
    )
    for area of @area.get("areas")
      @addAreaToView @area.get("areas")[area]
    for block of @area.get("blocks")
      @addBlockToView @area.get("blocks")[block]
    $("ul.ui-model-blocks", @el).remove()  if $("ul.ui-model-blocks", @el).children().length is 0
    if $("ul.ui-model-areas", @el).children().length is 0
      $("ul.ui-model-areas", @el).remove()
      makeSortable @el
    this

  addAreaToView: (area) ->
    areaElement = new Area()
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node_id
      displayClass: (if @area.get("bo_direction") is "v" then "inline" else "block")
    )
    $("ul.ui-model-areas", @el).append areaView.render().el

  addBlockToView: (block) ->
    blockElement = new Block()
    blockElement.set block
    blockView = new BlockView(
      block: blockElement
      displayClass: (if @area.get("bo_direction") is "v" then "inline" else "block")
    )
    $("ul.ui-model-blocks", @el).append blockView.render().el

  sendBlockData: ->
    if $("ul.ui-model-areas", @el).length == 0
      blocks = $("ul.resizable-" + @cid, @el).children()
      blockData = []
      for block in blocks
        if $('div[data-node-id]', block).length > 0
          blockData.push({'node_id' : $('div[data-node-id]', block)[0].getAttribute('data-node-id'), 'block_id' : $('div[data-block-id]', block)[0].getAttribute('data-block-id')})
        else if $('div[data-block-type]', block).length > 0
          blockData.push({'component' : $('div[data-block-type]', block)[0].getAttribute('data-block-type')})
      areaData = {}
      areaData['blocks'] = blockData
      $.ajax
        url: @area.get('links')._self_block
        method: 'POST'
        data: JSON.stringify(areaData)

  confirmRemoveBlock: (event) ->
    @removeBlock event  if confirm("Vous êtes sur le point de supprimer un bloc. Souhaitez-vous poursuivre cette action ?")  if @area.get("blocks").length > 0

  removeBlock: (event) ->
    event.parents("li").first().remove()
    refreshUl event.parents("ul").first()
    @sendBlockData()
)