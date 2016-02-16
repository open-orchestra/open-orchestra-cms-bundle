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

  showFormColumn: ->
    console.log('show')
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    new adminFormViewClass(
      url: @options.area.get("links")._self_form_column
      entityType: @options.entityType
    )

  ###*
   * @param {Object} el Jquery element
  ###
  updateToolbarPosition: (el)->
    el.removeClass("fixed")
    if $(window).scrollTop() > el.offset().top - el.height()
      el.addClass("fixed")
      el.width(el.parent().width())
