NodeView = OrchestraView.extend(
  el: '#content'

  events:
    'click i#none' : 'clickButton'

  initialize: (options) ->
    @node = options.node
    @options = options
    @options.multiLanguage = 
      language: @node.get('language')
      language_list: @node.get('links')._language_list
      path: 'showNodeWithLanguage'
    @options.multiStatus = 
      language: @node.get('language')
      version: @node.get('version')
      status_list: @node.get('links')._status_list
      status: @node.get('status')
      self_status_change: @node.get('links')._self_status_change
    @options.multiVersion = 
      language: @node.get('language')
      version: @node.get('version')
      self_version: @node.get('links')._self_version
      path: 'showNodeWithLanguageAndVersion'
    @options.duplicate = 
      language: @node.get('language')
      path : 'showNodeWithLanguage'
      self_duplicate: @node.get('links')._self_duplicate
    @events['click span.' + @cid] = 'clickButton'
    @events['click i.show-areas'] = 'showAreas'
    @events['click i.hide-areas'] = 'hideAreas'
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @loadTemplates [
      "nodeView"
      "areaView"
      "blockView"
      "elementTitle"
      "rightPanel"
    ]
    return

  clickButton: (event) ->
    $('.modal-title').text @node.get('name')
    url = @node.get('links')._self_form
    deleteurl = @node.get('links')._self_delete
    redirectUrl = appRouter.generateUrl "showNode",
      nodeId: @node.get('parent_id')
    confirmText = $(".delete-confirm-txt-" + @cid).text()
    confirmTitle = $(".delete-confirm-title-" + @cid).text()
    if @node.attributes.alias is ''
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
        confirmtext: confirmText
        confirmtitle: confirmTitle
        redirectUrl: redirectUrl
        generateId: true
      )
    else
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
        confirmtext: confirmText
        confirmtitle: confirmTitle
      )

  render: ->
    $(@el).html @renderTemplate('nodeView',
      node: @node
      cid: @cid
    )
    $('.js-widget-title', @$el).html @renderTemplate('elementTitle',
      element: @node
    )
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    @addListBlockToView()
    if @node.get('node_type') == 'page'
      @addPreviewLink()
      @addConfigurationButton()
      if @node.attributes.status.published
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
      node_id: @node.get('node_id'),
      node_published: @node.attributes.status.published
      domContainer: areaContainer
      viewContainer: @
    )
    areaContainer.addClass (if @node.get("bo_direction") is "h" then "bo-row" else "bo-column")
    return

  addPreviewLink: ->
    previewLinks = @node.get('preview_links')
    Backbone.Wreqr.radio.commands.execute 'preview_link', 'render', previewLinks

  addConfigurationButton: ->
    cid = @cid
    view = new PageConfigurationButtonView(
      cid: cid
    )

  addListBlockToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @node.get('links')._block_list
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

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('.area-toolbar').removeClass('shown')
)
