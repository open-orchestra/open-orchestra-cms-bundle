###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexView
###
class OpenOrchestra.AreaFlex.AreaFlexToolbarView extends OrchestraView

  extendView: ['OpenOrchestra.AreaFlex.AddRow']

  events:
    'click .add-row-action': 'showFormAddRow'
    'click .edit-column': 'showFormColumn'
    'click .delete-column': 'deleteColumn'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'areaView'
      'domContainer'
    ])
    @options.area = @options.areaView.options.area
    @options.entityType = @options.areaView.options.entityType
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexToolbarView"
    ]

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/areaFlex/areaFlexToolbarView', @options)
    @options.domContainer.html(@$el)
    context = @
    @updateToolbarPosition(@$el)
    $(window).bind 'scroll', () ->
      context.updateToolbarPosition(context.$el)
  ###*
   * Show form edit column
  ###
  showFormColumn: ->
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    url = @options.area.get("links")._self_form_column
    if url?
      new adminFormViewClass(
        url: url
        entityType: @options.entityType
      )

  ###*
   * @param {Object} el Jquery element
  ###
  updateToolbarPosition: (el) ->
    el.removeClass("fixed")
    if $(window).scrollTop() > el.offset().top - el.height()
      el.addClass("fixed")
      el.width(el.parent().width())

  ###*
   * Delete column
  ###
  deleteColumn: (event) ->
    event.stopImmediatePropagation()
    button = $(event.target)
    smartConfirm(
      'fa-trash-o',
      button.data('delete-confirm-question'),
      button.data('delete-confirm-explanation'),
      callBackParams:
        areaToolbarView: @
        message: button.data('delete-error-txt')
      yesCallback: (params) ->
        params.areaToolbarView.options.areaView.remove()
        $.ajax
          url: params.areaToolbarView.options.area.get("links")._self_delete_column
          method: "DELETE"
          message: params.message
    )
