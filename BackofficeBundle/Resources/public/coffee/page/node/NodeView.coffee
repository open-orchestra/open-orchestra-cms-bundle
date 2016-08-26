###*
 * @namespace OpenOrchestra:Page:Node
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Node or= {}

###*
 * @class NodeView
###
class OpenOrchestra.Page.Node.NodeView extends OpenOrchestra.Page.Common.AbstractPageView

  ###*
   * required options
   * {
   *   node: {object}
   *   domContainer: {object}
   * }
   *
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'node'
      'domContainer'
    ])
    @completeOptions @options.node,
      'multiLanguage': 'showNodeWithLanguage'
      'multiVersion': 'showNodeWithLanguageAndVersion'
      'newVersion': 'showNodeWithLanguage'
    @options.configuration = @options.node
    @options.entityType = 'node'
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/node/nodeView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle"
    ]
    return

  ###*
   * Render template
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/node/nodeView',
      node: @options.node
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
      element: @options.node
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addArea($('.page-container', @$el), @options.node.get('root_area'))
    @addConfigurationButton()
    return
