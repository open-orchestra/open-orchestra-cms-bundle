###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Block or= {}

###*
 * @class AreaView
###
class OpenOrchestra.Page.Block.BlockView extends OrchestraView

  events:
    'click .toolbar .block-remove': 'removeBlock'
    'click .toolbar .block-param': 'editBlock'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'block'
      'domContainer'
      'area'
    ])

    @options.entityType = 'block'
    @options.editable = @options.area.get('editable')
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockView"
    ]
    return

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/block/blockView', @options)
    @options.domContainer.append @$el

  ###*
   * Remove a block
  ###
  removeBlock: (event) ->
    event.stopPropagation()
    if @options.block.get('is_deletable')
      smartConfirm(
        'fa-trash-o',
        @$el.data('delete-confirm-question'),
        @$el.data('delete-confirm-explanation'),
        callBackParams:
          block: @options.block
          area: @options.area
        yesCallback: (params) ->
          params.area.removeBlock(params.block)
      )

  ###*
   * edit block
  ###
  editBlock: (event) ->
    event.stopPropagation()
    adminFormViewClass = appConfigurationView.getConfiguration('area', 'showAdminForm')
    new adminFormViewClass(
      url: @options.block.get('links')._self_form
      extendView: [ 'showVideo' ]
      entityType: 'block'
    )
