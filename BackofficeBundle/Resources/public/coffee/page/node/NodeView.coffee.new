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
 
    # This view not needed widgetLanguage template, there is loaded here to optimize number of request
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetLanguage"
    ]

  ###*
   * Render template
  ###
  render: ->
    @url = window.OpenOrchestra.staticTemplateConfig[@options.node.get('template_set')][@options.node.get('template')]
    
    $.ajax
      url: @url
      context: @
      success: (response) ->
        @setElement response
        @options.domContainer.html @$el
        $('.js-widget-title', @$el).html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
          element: @options.node
        )
        $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
		$('.block-open', @$el).each(function(){
		   if (area = @options.node.get('areas').get($(@).data('area-id'))
             @renderBlocks($(@), area)
           else
             @renderBlocks($(@))
		});
        @addConfigurationButton()
        return
    return

  renderBlocks: (domContainer, area) ->
    if (typeof blocks !== 'undefined')
      for block in area.get('blocks').models
        blockViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addBlock')
        new blockViewClass(
          domContainer: container
          block: block
          area: @options.area
        )
      @activateBlocksSortable()
      return
