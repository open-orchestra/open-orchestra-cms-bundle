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
    'click .delete-row': 'deleteRow'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'domContainer'
    ])
    @options.entityType = 'area-flex'
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
   * @param {object} event Jquery event
  ###
  deleteColumn: (event) ->
    url = @options.area.get("links")._self_delete_column
    @deleteArea(event, url) if url

  ###*
   * Delete row
   * @param {object} event Jquery event
  ###
  deleteRow: (event) ->
    url = @options.area.get("links")._self_delete_row
    @deleteArea(event, url) if url

  ###*
   * Delete area
   * @param {object} event Jquery event
   * @param {string} url
  ###
  deleteArea: (event, url) ->
    button = $(event.target)
    console.log(button)
    smartConfirm(
      'fa-trash-o',
      button.attr('data-delete-confirm-question'),
      button.attr('data-delete-confirm-explanation'),
      callBackParams:
        url: url
        message: button.attr('data-delete-error-txt')
      yesCallback: (params) ->
        $.ajax
          url: url
          method: "DELETE"
          message: params.message
          success: () ->
            Backbone.history.loadUrl(Backbone.history.fragment);
    )

