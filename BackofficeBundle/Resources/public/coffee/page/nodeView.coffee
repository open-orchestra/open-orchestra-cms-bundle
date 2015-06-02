NodeView = OrchestraView.extend(
  extendView : [ 'commonPage', 'addArea' ]

  initialize: (options) ->
    @options = @reduceOption(options, [
      'node'
      'domContainer'
    ])
    @completeOptions @options.node, 'path':
      'multiLanguage': 'showNodeWithLanguage'
      'multiVersion': 'showNodeWithLanguageAndVersion'
      'duplicate': 'showNodeWithLanguage'
    @options.configuration = @options.node
    @options.published = if @options.node.attributes.status then @options.node.attributes.status.published else true
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/blockView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/rightPanel"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/nodeView',
      node: @options.node
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @$el).html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
      element: @options.node
    )
    @addAreasToView(@options.node.get('areas'))
    @addListBlockToView()
    @addPreviewLink()
    @addConfigurationButton()
    if @options.node.attributes.status.published
      $('.js-widget-blockpanel', @$el).hide()
    return

  addPreviewLink: ->
    previewLinks = @options.node.get('preview_links')
    Backbone.Wreqr.radio.commands.execute 'preview_link', 'render', previewLinks

  addListBlockToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @options.node.get('links')._block_list
      success: (response) ->
        blockpanel = $('.js-widget-blockpanel', viewContext.$el)
        for i of response.blocks
          blockElement = new Block()
          blockElement.set response.blocks[i]
          response.blocks[i] = viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/blockView', 
            block : blockElement
          )
        blockpanel.html viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/rightPanel', response)
        Backbone.Wreqr.radio.commands.execute 'viewport', 'init', blockpanel
        $(window).resize ->
          Backbone.Wreqr.radio.commands.execute 'viewport', 'init'
          return
        $(window).add('div[role="content"]').scroll ->
          Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
          return

)
