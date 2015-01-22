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
        triggers: [
          {
            event: "keyup input.alias-source"
            name: "refreshAlias"
            fct: refreshAlias
          }
          {
            event: "blur input.alias-dest"
            name: "stopRefreshAlias"
            fct: stopRefreshAlias
          }
        ]
      )
    else
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
        confirmtext: confirmText
      )

  render: ->
    title = @renderTemplate('elementTitle',
      element: @node
    )
    $(@el).html @renderTemplate('nodeView',
      node: @node
      title: title
      cid: @cid
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    $('.js-widget-blockpanel', @$el).html($('#generated-panel', @$el).html()).show()
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    @addExistingBlockToView()
    if @node.get('node_type') == 'page'
      @addPreviewLink()
      @addConfigurationButton()
      if @node.attributes.status.published
        $('.ui-model *', @el).unbind()
        $('.js-widget-blockpanel', @$el).hide()
        $('span.' + @cid, @el).addClass('disabled')
      else
        $("ul.ui-model-areas, ul.ui-model-blocks", @$el).each ->
          refreshUl $(this)
    return

  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node.get('node_id'),
      node_published: @node.attributes.status.published
      displayClass: (if @node.get("bo_direction") is "h" then "bo-row" else "bo-column")
      el: @$el.find('ul.ui-model-areas').first()
    )
    return

  addPreviewLink: ->
    previewLink = @node.get('links')._self_preview
    view = new PreviewLinkView(
      previewLink: previewLink
    )

  addConfigurationButton: ->
    cid = @cid
    view = new PageConfigurationButtonView(
      cid: cid
    )

  addExistingBlockToView: ->
    viewContext = @
    $.ajax
      type: "GET"
      url: @node.get('links')._existing_block
      success: (response) ->
        $('.rigth-panel-blocks', viewContext.$el).append(response)

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('div.toolbar-layer.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('div.toolbar-layer.area-toolbar').removeClass('shown')
)
