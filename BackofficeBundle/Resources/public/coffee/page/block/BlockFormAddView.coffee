###*
 * @namespace OpenOrchestra:Page:Block
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Block or= {}

###*
 * @class BlockFormAddView
###
class OpenOrchestra.Page.Block.BlockFormAddView extends OrchestraModalView

  events:
    'click .item-block': 'addBlock'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'urlBlockList'
      'title'
      'domContainer'
      'entityType'
      'area'
    ])
    @options.html = ''
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockListView'
    ]
    return

  ###*
   * Render modal an list block
  ###
  render: ->
    super()
    displayLoader('.modal-body', @$el)
    viewContext = @
    $.ajax
      type: "GET"
      url: @options.urlBlockList
      success: (response) ->
          viewContext.options.blocks = new BlockCollection
          viewContext.options.blocks.set(response.blocks)
          $('.modal-body', viewContext.$el).html viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockListView',
            blocks : viewContext.options.blocks
          )
    return

  ###*
   * Add block in area
  ###
  addBlock: (event) ->
    blockId = $(event.currentTarget).attr('data-block-id');
    block = @options.blocks.get(blockId);
    @options.area.addBlock(block)
    @options.domContainer.modal "hide"
    @unbind()
    @remove()

