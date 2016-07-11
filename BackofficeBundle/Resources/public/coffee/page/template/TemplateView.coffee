###*
 * @namespace OpenOrchestra:Page:Template
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Template or= {}

###*
 * @class TemplateView
###
class OpenOrchestra.Page.Template.TemplateView extends OpenOrchestra.Page.Common.AbstractPageView

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
    @options.entityType = 'template'
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/template/templateView"
    ]
    return

  ###*
   * Render template
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/template/templateView',
      template: @options.template
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addArea($('.page-container', @$el), @options.template.get('area'))
    @addConfigurationButton()
    return
