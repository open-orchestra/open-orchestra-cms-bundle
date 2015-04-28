NodeView = OrchestraView.extend(
  el: '#content'

  events:
    'click i.test': 'showAreas'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'node'
      'extendView'
    ])
    @options.multiLanguage = 
      language: @options.node.get('language')
      language_list: @options.node.get('links')._language_list
      path: 'showNodeWithLanguage'
    @options.multiStatus = 
      language: @options.node.get('language')
      version: @options.node.get('version')
      status_list: @options.node.get('links')._status_list
      status: @options.node.get('status')
      self_status_change: @options.node.get('links')._self_status_change
    @options.multiVersion = 
      language: @options.node.get('language')
      version: @options.node.get('version')
      self_version: @options.node.get('links')._self_version
      path: 'showNodeWithLanguageAndVersion'
    @options.duplicate = 
      language: @options.node.get('language')
      path : 'showNodeWithLanguage'
      self_duplicate: @options.node.get('links')._self_duplicate
    @options.pageConfiguration = @options.node
    @loadTemplates [
      "nodeView"
      "blockView"
      "elementTitle"
      "rightPanel"
    ]
    return

  render: ->
    $(@el).html @renderTemplate('nodeView',
      node: @options.node
      cid: @cid
    )
    $('.js-widget-title', @$el).html @renderTemplate('elementTitle',
      element: @options.node
    )
    for area of @options.node.get('areas')
      @addAreaToView(@options.node.get('areas')[area])
    @addListBlockToView()
    if @options.node.get('node_type') == 'page'
      @addPreviewLink()
      @addConfigurationButton()
      if @options.node.attributes.status.published
        $('.js-widget-blockpanel', @$el).hide()
      else
        $(".ui-model-areas, .ui-model-blocks", @$el).each ->
          refreshUl $(this)
    return

  addAreaToView: (area) ->
    areaContainer = @$el.find('.ui-model-areas').first()
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @options.node.get('node_id'),
      node_published: @options.node.attributes.status.published
      domContainer: areaContainer
      viewContainer: @
    )
    areaContainer.addClass (if @options.node.get("bo_direction") is "h" then "bo-row" else "bo-column")
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
          response.blocks[i] = viewContext.renderTemplate('blockView', 
            block : blockElement
          )
        blockpanel.html viewContext.renderTemplate('rightPanel', response)
        Backbone.Wreqr.radio.commands.execute 'viewport', 'init', blockpanel
        $(window).resize ->
          Backbone.Wreqr.radio.commands.execute 'viewport', 'init'
          return
        $(window).add('div[role="content"]').scroll ->
          Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
          return

)
