###*
 * @namespace OpenOrchestra:TemplateFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.TemplateFlex or= {}

###*
 * @class TemplateFlexView
###
class OpenOrchestra.TemplateFlex.TemplateFlexView extends OrchestraView

  ###*
   * required options
   * {
   *   template: {object}
   *   domContainer: {object}
   * }
   *
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'template'
      'domContainer'
    ])
    @options.configuration = @options.template
    @options.entityType = 'template-flex'
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/templateFlex/templateFlexView"
    ]
    return

  ###*
   * Render template
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/templateFlex/templateFlexView',
      template: @options.template
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addArea($('.template-flex-container', @$el), @options.template.get('areas'))
    return

  ###*
   * @param {Object} container Jquery selector
   * @param {Object} areas List of areas to add in container
  ###
  addArea: (container, areas) ->
    for name, area of areas
      areaModel = new Area
      areaModel.set area
      areaViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addAreaFlex')
      new areaViewClass(
        area: areaModel
        domContainer: container
        toolbarContainer: $('.toolbar-container', @$el)
      )
