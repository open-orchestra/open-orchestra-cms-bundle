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
      "refresh:rightPanel"
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
            event: "focusout input.generate-id-source"
            name: "generateId"
            fct: generateId
          }
          {
            event: "blur input.generate-id-dest"
            name: "stopGenerateId"
            fct: stopGenerateId
          }
        ]
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
    blockpanel = $('.js-widget-blockpanel', @$el)
    blockpanel.html @renderTemplate('refresh:rightPanel')
    $('.js-widget-title', @$el).html @renderTemplate('elementTitle',
      element: @node
    )
    Backbone.Wreqr.radio.commands.execute 'viewport', 'init', blockpanel
    $(window).resize ->
      Backbone.Wreqr.radio.commands.execute 'viewport', 'resize'
      return
    $(window).add('div[role="content"]').scroll ->
      Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
      return
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
        $('.toolbar', @$el).hide()
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
    previewLinks = @node.get('preview_links')
    Backbone.Wreqr.radio.commands.execute 'preview_link', 'render', previewLinks

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
        $('.right-panel-blocks', viewContext.$el).append(response)

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('div.toolbar-layer.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('div.toolbar-layer.area-toolbar').removeClass('shown')
)
