BlockView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-blocks'
  events:
    'click i.block-param': 'paramBlock'
    'click i.block-remove': 'confirmRemoveBlock'
  initialize: (options) ->
    @block = options.block
    @height = options.height
    @direction = options.direction || 'height'
    @displayClass = (if @direction is "width" then "inline" else "block")
    _.bindAll this, "render"
    @blockTemplate = _.template($('#blockView').html())
    return
  paramBlock: (event) ->
    $('.modal-title').text 'Please wait ...'
    view = new adminFormView(url: @block.get('links')._self_form)
  confirmRemoveBlock: (event) ->
    if confirm 'Vous êtes sur le point de supprimer un bloc. Souhaitez-vous poursuivre cette action ?'
      @removeBlock event
  removeBlock: (event) ->
    switchLoaderFullPage('on')
    that = this
    $.ajax
      url: @block.get('links')._self_remove
      method: 'POST'
      success: (response) ->
        showNode $("#nav-node-" + that.block.get('node_id')).data("url")
        return
      error: ->
        $('.modal-title').text 'Block removal'
        $('.modal-body').html 'Erreur durant la suppression'
        switchLoaderFullPage('off')
        $("#OrchestraBOModal").modal "show"
    return
  render: ->
    $(@el).attr('style', @direction + ':' + @height + '%').addClass(@displayClass).html @blockTemplate(
      block: @block
      height: @height
      cid: @cid
    )
    this
)
