NodeView = OrchestraView.extend(
  el: '#content'

  events:
    'click i#none' : 'clickButton'
    'change select#selectbox': 'changeVersion'
    'click a#btn-new-version': 'duplicateNode'

  initialize: (options) ->
    @node = options.node
    @version = @node.get('version')
    @language = @node.get('language')
    @options.multiLanguage = 
      language: @node.get('language')
      language_list: @node.get('links')._language_list
      path: 'showNodeWithLanguage'
      path_option: {nodeId : @node.get('node_id')}
    @multiStatus = 
      status: @node.get('status')
      status_list: @node.get('links')._status_list
      language: @node.get('language')
      version: @node.get('version')
      path: 'showNodeWithLanguageAndVersion'
      path_option: {nodeId : @node.get('node_id')}
      status_change_link: @node.get('links')._self_status_change

    @version = @node.get('version')
    @language = @node.get('language')
    @events['click i.' + @node.cid] = 'clickButton'
    @events['click i.show-areas'] = 'showAreas'
    @events['click i.hide-areas'] = 'hideAreas'
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @loadTemplates [
      "nodeView"
      "nodeTitle"
      "widgetStatus"
      "areaView"
      "nodeChoice"
      "blockView"
    ]
    return

  clickButton: (event) ->
    $('.modal-title').text @node.get('name')
    url = @node.get('links')._self_form
    deleteurl = @node.get('links')._self_delete
    redirectUrl = appRouter.generateUrl "showNode",
      nodeId: @node.get('parent_id')
    confirmText = $(".delete-confirm-txt-"+@node.cid).text()
    if @node.attributes.alias is ''
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
        confirmtext: confirmText
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

  duplicateNode: ->
    viewContext = @
    redirectRoute = appRouter.generateUrl( "showNodeWithLanguage",
      nodeId: @node.get('node_id')
      language: @node.get('language')
    )
    $.ajax
      url: @node.get('links')._self_duplicate
      method: 'POST'
      success: (response) ->
        Backbone.history.loadUrl(redirectRoute)
    return

  renderWidgetStatus: ->
    viewContext = this
    $.ajax
      type: "GET"
      data:
        language: @node.get('language')
        version: @node.get('version')
      url: @node.get('links')._status_list
      success: (response) ->
        widgetStatus = viewContext.renderTemplate('widgetStatus',
          current_status: viewContext.node.get('status')
          statuses: response.statuses
          status_change_link: viewContext.node.get('links')._self_status_change
        )
        addCustomJarvisWidget(widgetStatus)
        return

  render: ->
    title = @renderTemplate('nodeTitle',
      node: @node
    )
    $(@el).html @renderTemplate('nodeView',
      node: @node
      title: title
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    $('.js-widget-blockpanel', @$el).html($('#generated-panel', @$el).html()).show()
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    @addExistingBlockToView()
    if @node.get('node_type') == 'page'
      @renderWidgetStatus()
      @addVersionToView()
      @addPreviewLink()
      if @node.attributes.status.published
        $('.ui-model *', @el).unbind()
        $('.js-widget-blockpanel', @$el).hide()
        $('span.action', @el).hide()
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
      displayClass: (if @node.get("bo_direction") is "v" then "inline" else "block")
      el: @$el.find('ul.ui-model-areas').first()
    )
    return

  addVersionToView: ->
    viewContext = this
    $.ajax
      type: "GET"
      url: @node.get('links')._self_version
      success: (response) ->
        nodeCollection = new NodeCollectionElement
        nodeCollection.set response
        for nodeVersion of nodeCollection.get('nodes')
          viewContext.addChoiceToSelectBox(nodeCollection.get('nodes')[nodeVersion])
        return

  addChoiceToSelectBox: (nodeVersion) ->
    nodeVersionElement = new Node
    nodeVersionElement.set nodeVersion
    view = new NodeVersionView(
      node: nodeVersionElement
      version: @version
      el: this.$el.find('optgroup#versions')
    )

  changeVersion: (event) ->
    redirectRoute = appRouter.generateUrl('showNodeWithLanguageAndVersion',
      nodeId: @node.get('node_id'),
      language: @language,
      version: event.currentTarget.value
    )
    Backbone.history.navigate(redirectRoute , {trigger: true})
    return

  addPreviewLink: ->
    previewLink = @node.get('links')._self_preview
    view = new PreviewLinkView(
      previewLink: previewLink
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
